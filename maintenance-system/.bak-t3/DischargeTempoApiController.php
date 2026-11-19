<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\FormataTempos;
use App\Http\Controllers\Controller;
use App\Models\Discharge;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DischargeTempoApiController extends Controller
{
    use FormataTempos;

    /** Fuso do pátio: usado só nas horas H:i legadas; os carimbos ficam em UTC. */
    private const FUSO_LOCAL = 'Africa/Maputo';

    private const ROTULOS = [
        'pending'    => 'Pendente',
        'loaded'     => 'Carregado',
        'in_transit' => 'Em trânsito',
        'weighing'   => 'A pesar',
        'confirmed'  => 'Confirmado',
        'finished'   => 'Terminado',
    ];

    /** ② Saída do porto: loaded → in_transit */
    public function saiuPorto(Discharge $discharge)
    {
        return $this->avancar($discharge, 'loaded', 'in_transit', 'saiu_porto', function ($d, $agora) {
            $d->hora_saida_porto = $agora->copy()->timezone(self::FUSO_LOCAL)->format('H:i');
        });
    }

    /** ③ Chegada à balança: in_transit → weighing */
    public function chegouBalanca(Discharge $discharge)
    {
        return $this->avancar($discharge, 'in_transit', 'weighing', 'chegou_balanca', function ($d, $agora) {
            if (array_key_exists('hora_chegada_balanca', $d->getAttributes()) && ! $d->hora_chegada_balanca) {
                $d->hora_chegada_balanca = $agora->copy()->timezone(self::FUSO_LOCAL)->format('H:i');
            }
        });
    }

    /** ④ Saída da descarga: confirmed → finished (Terminado) */
    public function saiuDescarga(Discharge $discharge)
    {
        return $this->avancar($discharge, 'confirmed', 'finished', 'saiu_descarga');
    }

    /** Recua uma etapa (só gestor/super-admin). */
    public function desfazer(Discharge $discharge)
    {
        $user = auth()->user();
        abort_unless(
            $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super-admin', 'gestor']),
            403,
            'Sem permissão para desfazer.'
        );

        $recuo = [
            'finished'   => ['confirmed', 'saiu_descarga'],
            'weighing'   => ['in_transit', 'chegou_balanca'],
            'in_transit' => ['loaded', 'saiu_porto'],
        ];

        $d = DB::transaction(function () use ($discharge, $recuo) {
            $d = Discharge::lockForUpdate()->findOrFail($discharge->id);

            if (! isset($recuo[$d->status])) {
                $this->recusar('Não há etapa para desfazer a partir de «' . $this->rotulo($d->status) . '».');
            }

            [$para, $etapa] = $recuo[$d->status];

            if ($d->status === 'weighing' && ($d->peso_bruto !== null || $d->tara !== null)) {
                $this->recusar('Este camião já tem pesagem registada. Corrija-a na Central.');
            }

            $d->forceFill([
                $etapa . '_em'  => null,
                $etapa . '_por' => null,
                'status'        => $para,
            ]);

            if ($etapa === 'saiu_porto') {
                $d->hora_saida_porto = null;
            }
            if ($etapa === 'chegou_balanca' && array_key_exists('hora_chegada_balanca', $d->getAttributes())) {
                $d->hora_chegada_balanca = null;
            }

            $d->save();

            return $d;
        });

        return response()->json($this->resposta($d));
    }

    private function avancar(Discharge $discharge, string $de, string $para, string $etapa, ?\Closure $extra = null)
    {
        $d = DB::transaction(function () use ($discharge, $de, $para, $etapa, $extra) {
            $d = Discharge::lockForUpdate()->findOrFail($discharge->id);

            $colEm  = $etapa . '_em';
            $colPor = $etapa . '_por';

            // Toque repetido / etapa já feita noutro sítio: não dá erro. Só completa a hora em falta.
            if ($d->status === $para) {
                if (! $d->{$colEm}) {
                    $d->forceFill([$colEm => now(), $colPor => auth()->id()])->save();
                }

                return $d;
            }

            if ($d->status !== $de) {
                $this->recusar('Etapa não permitida: o camião está «' . $this->rotulo($d->status)
                    . '» e esta etapa exige «' . $this->rotulo($de) . '».');
            }

            $agora = now();

            $d->forceFill([
                $colEm   => $agora,
                $colPor  => auth()->id(),
                'status' => $para,
            ]);

            if ($extra) {
                $extra($d, $agora);
            }

            $d->save();

            return $d;
        });

        return response()->json($this->resposta($d));
    }

    private function resposta(Discharge $d): array
    {
        return [
            'id'               => $d->id,
            'status'           => $d->status,
            'status_label'     => $this->rotulo($d->status),
            'hora_saida_porto' => $d->hora_saida_porto ? substr((string) $d->hora_saida_porto, 0, 5) : null,
            'tempos'           => $this->temposDetalhe($d),
        ];
    }

    private function rotulo(?string $status): string
    {
        return self::ROTULOS[$status] ?? (string) $status;
    }

    private function recusar(string $mensagem): void
    {
        throw ValidationException::withMessages(['status' => [$mensagem]]);
    }
}
