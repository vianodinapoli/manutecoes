<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartaNova;
use App\Models\CartaNovaCamiao;
use App\Models\DischargeOperation;
use App\Services\CartaNovaImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use RuntimeException;

class CartaNovaApiController extends Controller
{
    /** Listas (uma por DO) carregadas nesta operação. */
    public function index(DischargeOperation $operation): JsonResponse
    {
        $cartas = $operation->cartasNovas()
            ->orderBy('do_numero')
            ->get(['id', 'do_numero', 'ficheiro', 'total_camioes', 'created_at', 'updated_at']);

        return response()->json([
            'tem_lista' => $cartas->isNotEmpty(),
            'cartas'    => $cartas,
        ]);
    }

    /** Carrega o Excel da carta nova. O DO é lido do próprio ficheiro. */
    public function store(Request $request, DischargeOperation $operation, CartaNovaImport $import): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'file.required' => 'Escolha o ficheiro Excel da carta nova.',
            'file.mimes'    => 'O ficheiro tem de ser Excel (.xlsx ou .xls).',
            'file.max'      => 'O ficheiro é demasiado grande (máximo 5 MB).',
        ]);

        $ficheiro = $request->file('file');

        try {
            $r = $import->importar(
                $operation,
                $ficheiro->getRealPath(),
                $ficheiro->getClientOriginalName(),
                $request->user()?->id,
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (SpreadsheetException $e) {
            report($e);
            return response()->json(['message' => 'Não consegui ler o ficheiro Excel. Confirme que não está corrompido.'], 422);
        }

        return response()->json([
            'message'    => $r['substituiu']
                ? "Lista do {$r['do_numero']} substituída: {$r['importados']} camiões."
                : "Lista do {$r['do_numero']} carregada: {$r['importados']} camiões.",
            'carta_id'   => $r['carta']->id,
            'do_numero'  => $r['do_numero'],
            'linhas'     => $r['linhas'],
            'importados' => $r['importados'],
            'duplicados' => $r['duplicados'],
            'ignorados'  => $r['ignorados'],
            'substituiu' => $r['substituiu'],
        ], $r['substituiu'] ? 200 : 201);
    }

    /**
     * Confirma se a matrícula está na lista desta operação.
     * Devolve todos os motoristas associados (a mesma matrícula pode ter mais do que um).
     */
    public function verificar(Request $request, DischargeOperation $operation): JsonResponse
    {
        $request->validate(['matricula' => ['required', 'string', 'min:3', 'max:20']]);

        $matricula = CartaNovaCamiao::normalizarMatricula($request->query('matricula'));

        if (! $operation->cartasNovas()->exists()) {
            return response()->json([
                'tem_lista'  => false,
                'na_lista'   => false,
                'matricula'  => $matricula,
                'motoristas' => [],
            ]);
        }

        $motoristas = CartaNovaCamiao::query()
            ->where('matricula', $matricula)
            ->whereHas('carta', fn ($q) => $q->where('operation_id', $operation->id))
            ->with('carta:id,do_numero')
            ->orderBy('id')
            ->get()
            ->map(fn (CartaNovaCamiao $c) => [
                'primeiro_nome'  => $c->primeiro_nome,
                'ultimo_nome'    => $c->ultimo_nome,
                'nome_completo'  => $c->nome_completo,
                'carta_conducao' => $c->carta_conducao,
                'do_numero'      => $c->carta->do_numero,
            ])
            ->values();

        return response()->json([
            'tem_lista'  => true,
            'na_lista'   => $motoristas->isNotEmpty(),
            'matricula'  => $matricula,
            'motoristas' => $motoristas,
        ]);
    }

    /** Remove a lista de um DO (por exemplo, carregada por engano). */
    public function destroy(DischargeOperation $operation, CartaNova $cartaNova): JsonResponse
    {
        abort_unless($cartaNova->operation_id === $operation->id, 404);

        $do = $cartaNova->do_numero;
        $cartaNova->delete(); // os camiões vão em cascata

        return response()->json(['message' => "Lista do {$do} removida."]);
    }

    /** Sugestões de matrículas desta operação, para o campo de pesquisa do tablet. */
public function pesquisar(Request $request, DischargeOperation $operation): JsonResponse
{
    $q = CartaNovaCamiao::normalizarMatricula((string) $request->query('q', ''));

    if (strlen($q) < 2) {
        return response()->json(['resultados' => []]);
    }

    $resultados = CartaNovaCamiao::query()
        ->where('matricula', 'like', "%{$q}%")
        ->whereHas('carta', fn ($qr) => $qr->where('operation_id', $operation->id))
        ->with('carta:id,do_numero')
        ->orderBy('matricula')
        ->limit(8)
        ->get()
        ->map(fn (CartaNovaCamiao $c) => [
            'matricula'      => $c->matricula,
            'nome_completo'  => $c->nome_completo,
            'carta_conducao' => $c->carta_conducao,
            'do_numero'      => $c->carta->do_numero,
        ])
        ->values();

    return response()->json(['resultados' => $resultados]);
}
}
