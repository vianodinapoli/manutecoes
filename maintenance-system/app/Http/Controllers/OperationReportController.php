<?php

namespace App\Http\Controllers;

use App\Models\DischargeOperation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperationReportController extends Controller
{
    public function confirmed(Request $request, DischargeOperation $operation)
    {
        $data = $request->validate([
            'de'  => ['required', 'date'],
            'ate' => ['required', 'date', 'after_or_equal:de'],
        ]);

        // A hora escolhida é hora de Maputo; converte para o fuso da aplicação
        // (onde os datetimes estão guardados).
        $appTz = config('app.timezone');
        $de  = Carbon::parse($data['de'], 'Africa/Maputo')->setTimezone($appTz);
        $ate = Carbon::parse($data['ate'], 'Africa/Maputo')->setTimezone($appTz);

        // Confirmado = pesagem completa. A hora de confirmação é a mais tardia
        // entre peso_bruto_em e tara_em, e tem de cair dentro do intervalo.
        $q = $operation->discharges()
            ->where('status', 'confirmed')
            ->whereNotNull('peso_bruto_em')
            ->whereNotNull('tara_em')
            ->where('peso_bruto_em', '<=', $ate)
            ->where('tara_em', '<=', $ate)
            ->where(function ($w) use ($de) {
                $w->where('peso_bruto_em', '>=', $de)
                  ->orWhere('tara_em', '>=', $de);
            });

        $tot = (clone $q)->selectRaw(
            'COUNT(*) as camioes,
             COALESCE(SUM(COALESCE(sacos_confirmados, total_sacos)), 0) as sacos,
             COALESCE(SUM(peso_liquido), 0) as peso_kg'
        )->first();

        return [
            'camioes'   => (int) $tot->camioes,
            'sacos'     => (int) $tot->sacos,
            'toneladas' => round(((float) $tot->peso_kg) / 1000, 3),
        ];
    }
}
