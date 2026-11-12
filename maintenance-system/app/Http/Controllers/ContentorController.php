<?php

namespace App\Http\Controllers;

use App\Models\Contentor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContentorController extends Controller
{
    /** Dias no parque a partir dos quais o contentor é considerado atrasado. */
    private const LIMITE_DIAS = 7;

    public function index()
    {
        $todos  = Contentor::orderByDesc('id')->get();
        $limite = self::LIMITE_DIAS;

        // Fisicamente no recinto = tudo menos "em trânsito" e "saiu"
        $dentro = $todos->reject(fn ($c) => in_array($c->estado, ['em_transito', 'saiu']));

        $kpis = [
            'total'      => $todos->count(),
            'noParque'   => $dentro->count(),
            'emTransito' => $todos->where('estado', 'em_transito')->count(),
            'carregados' => $todos->where('estado', 'carregado')->count(),
            'saiu'       => $todos->where('estado', 'saiu')->count(),
            'atrasados'  => $dentro->filter(fn ($c) => ($c->dias_no_parque ?? 0) > $limite)->count(),
            'peso'       => $dentro->sum(fn ($c) => (float) $c->peso_kg) / 1000,
            'limite'     => $limite,
        ];

        // Entradas por mês (últimos 12 meses)
        $inicio = now()->startOfMonth()->subMonths(11);
        $meses = [];
        $entradasMensais = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $inicio->copy()->addMonths($i);
            $meses[] = $m->format('m/Y');
            $entradasMensais[] = $todos->filter(
                fn ($c) => $c->data_entrada && $c->data_entrada->format('Y-m') === $m->format('Y-m')
            )->count();
        }

        // Por estado
        $porEstado = collect(Contentor::ESTADOS)
            ->map(fn ($label, $k) => ['nome' => $label, 'total' => $todos->where('estado', $k)->count()])
            ->filter(fn ($e) => $e['total'] > 0)
            ->values();

        // Clientes com mais contentores no parque
        $topClientes = $dentro->filter(fn ($c) => filled($c->cliente))
            ->groupBy('cliente')
            ->map->count()
            ->sortDesc()
            ->take(8);

        return view('contentores.index', [
            'contentores'     => $todos,
            'kpis'            => $kpis,
            'meses'           => $meses,
            'entradasMensais' => $entradasMensais,
            'porEstado'       => $porEstado,
            'topClientes'     => $topClientes,
        ]);
    }

    public function store(Request $request)
    {
        $dados = $this->validar($request);
        $dados['criado_por'] = auth()->id();
        Contentor::create($dados);

        return redirect()->route('contentores.index')->with('success', 'Contentor registado.');
    }

    public function update(Request $request, Contentor $contentor)
    {
        $contentor->update($this->validar($request, $contentor));

        return redirect()->route('contentores.index')->with('success', 'Contentor atualizado.');
    }

    public function destroy(Contentor $contentor)
    {
        $contentor->delete();

        return redirect()->route('contentores.index')->with('success', 'Contentor apagado.');
    }

    private function validar(Request $request, ?Contentor $contentor = null): array
    {
        $request->merge(['numero' => strtoupper(preg_replace('/[\s-]/', '', (string) $request->numero))]);

        $dados = $request->validate([
            'numero' => ['required', 'regex:/^[A-Z]{4}\d{7}$/',
                Rule::unique('contentores', 'numero')->ignore($contentor?->id),
                function ($attr, $value, $fail) {
                    if (! self::isoValido($value)) {
                        $fail('Dígito de controlo ISO 6346 inválido.');
                    }
                }],
            'tipo'         => ['required', Rule::in(Contentor::TIPOS)],
            'estado'       => ['required', Rule::in(array_keys(Contentor::ESTADOS))],
            'armador'      => 'nullable|string|max:255',
            'cliente'      => 'nullable|string|max:255',
            'navio'        => 'nullable|string|max:255',
            'bl'           => 'nullable|string|max:255',
            'selo'         => 'nullable|string|max:255',
            'peso_kg'      => 'nullable|numeric|min:0',
            'localizacao'  => 'nullable|string|max:255',
            'data_entrada' => 'nullable|date',
            'data_saida'   => 'nullable|date|after_or_equal:data_entrada',
            'observacoes'  => 'nullable|string',
        ], [
            'numero.regex' => 'Formato inválido. Use 4 letras e 7 dígitos (ex.: MSKU1234565).',
        ]);

        $dados['tamanho'] = str_starts_with($dados['tipo'], '40') ? 40 : 20;

        return $dados;
    }

    private static function isoValido(string $n): bool
    {
        $vals = [];
        $v = 10;
        foreach (range('A', 'Z') as $c) {
            if ($v % 11 === 0) {
                $v++;
            }
            $vals[$c] = $v++;
        }

        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $c = $n[$i];
            $soma += (ctype_alpha($c) ? $vals[$c] : (int) $c) * (2 ** $i);
        }

        return ($soma % 11) % 10 === (int) $n[10];
    }
}