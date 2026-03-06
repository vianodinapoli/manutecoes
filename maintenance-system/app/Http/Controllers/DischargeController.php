<?php

namespace App\Http\Controllers;

use App\Models\Discharge;
use Illuminate\Http\Request;

class DischargeController extends Controller
{
    public function index()
    {
        $discharges = Discharge::with(['registeredBy', 'confirmedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('discharges.index', compact('discharges'));
    }

    public function create()
    {
        return view('discharges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'data'             => 'required|date',
            'motorista'        => 'required|string|max:100',
            'matricula'        => 'required|string|max:20',
            'transportadora'   => 'required|string|max:100',
            'numero_guia'      => 'required|string|unique:descargas,numero_guia',
            'hora_saida_porto' => 'nullable|date_format:H:i',
            'sacos_alta'       => 'required_without:sacos_baixa|integer|min:0',
            'peso_saco_alta'   => 'required_if:sacos_alta,>0|numeric|min:0',
            'sacos_baixa'      => 'required_without:sacos_alta|integer|min:0',
            'peso_saco_baixa'  => 'required_if:sacos_baixa,>0|numeric|min:0',
        ]);

        $sacosAlta  = (int) $request->sacos_alta;
        $sacosBaixa = (int) $request->sacos_baixa;
        $pesoAlta   = (float) $request->peso_saco_alta;
        $pesoBaixa  = (float) $request->peso_saco_baixa;

        $totalAlta   = $sacosAlta * $pesoAlta;
        $totalBaixa  = $sacosBaixa * $pesoBaixa;
        $totalSacos  = $sacosAlta + $sacosBaixa;
        $pesoEstimado = $totalAlta + $totalBaixa;

        $discharge = Discharge::create([
            'data'               => $request->data,
            'motorista'          => $request->motorista,
            'matricula'          => $request->matricula,
            'transportadora'     => $request->transportadora,
            'numero_guia'        => $request->numero_guia,
            'hora_saida_porto'   => $request->hora_saida_porto,
            'registado_por'      => auth()->id(),
            'sacos_alta'         => $sacosAlta,
            'peso_saco_alta'     => $pesoAlta,
            'total_alta'         => $totalAlta,
            'sacos_baixa'        => $sacosBaixa,
            'peso_saco_baixa'    => $pesoBaixa,
            'total_baixa'        => $totalBaixa,
            'total_sacos'        => $totalSacos,
            'peso_total_estimado' => $pesoEstimado,
            'status'             => 'in_transit',
        ]);

        return redirect()->route('discharges.index')
            ->with('success', 'Descarga registada com sucesso! Aguarda confirmação na balança.');
    }

    public function show(Discharge $discharge)
    {
        return view('discharges.show', compact('discharge'));
    }

    public function edit(Discharge $discharge)
    {
        return view('discharges.edit', compact('discharge'));
    }

    public function update(Request $request, Discharge $discharge)
    {
        $request->validate([
            'data'           => 'required|date',
            'motorista'      => 'required|string|max:100',
            'matricula'      => 'required|string|max:20',
            'transportadora' => 'required|string|max:100',
            'numero_guia'    => 'required|string|unique:descargas,numero_guia,' . $discharge->id,
            'hora_saida_porto' => 'nullable|date_format:H:i',
        ]);

        $sacosAlta  = (int) $request->sacos_alta;
        $sacosBaixa = (int) $request->sacos_baixa;
        $pesoAlta   = (float) $request->peso_saco_alta;
        $pesoBaixa  = (float) $request->peso_saco_baixa;

        $discharge->update([
            'data'               => $request->data,
            'motorista'          => $request->motorista,
            'matricula'          => $request->matricula,
            'transportadora'     => $request->transportadora,
            'numero_guia'        => $request->numero_guia,
            'hora_saida_porto'   => $request->hora_saida_porto,
            'sacos_alta'         => $sacosAlta,
            'peso_saco_alta'     => $pesoAlta,
            'total_alta'         => $sacosAlta * $pesoAlta,
            'sacos_baixa'        => $sacosBaixa,
            'peso_saco_baixa'    => $pesoBaixa,
            'total_baixa'        => $sacosBaixa * $pesoBaixa,
            'total_sacos'        => $sacosAlta + $sacosBaixa,
            'peso_total_estimado' => ($sacosAlta * $pesoAlta) + ($sacosBaixa * $pesoBaixa),
        ]);

        return redirect()->route('discharges.index')->with('success', 'Descarga atualizada!');
    }

    public function confirm(Request $request, Discharge $discharge)
    {
        $request->validate([
            'hora_chegada_balanca' => 'required|date_format:H:i',
            'peso_bruto'           => 'required|numeric|min:0',
            'tara'                 => 'required|numeric|min:0',
            'sacos_confirmados'    => 'required|integer|min:1',
            'observacoes'          => 'nullable|string|max:500',
        ]);

        $discharge->hora_chegada_balanca = $request->hora_chegada_balanca;
        $discharge->peso_bruto           = $request->peso_bruto;
        $discharge->tara                 = $request->tara;
        $discharge->peso_liquido         = $request->peso_bruto - $request->tara;
        $discharge->sacos_confirmados    = $request->sacos_confirmados;
        $discharge->observacoes          = $request->observacoes;
        $discharge->confirmado_por       = auth()->id();

        $discharge->calculateTransportTime();
$discharge->status = 'confirmed';    
    $discharge->save();

        $message = $discharge->status === 'in_transit'
            ? '⚠️ Confirmado com divergência no número de sacos!'
            : '✅ Descarga confirmada com sucesso!';

        return redirect()->route('discharges.index')->with('success', $message);
    }

    public function destroy(Discharge $discharge)
    {
        $discharge->delete();
        return redirect()->route('discharges.index')->with('success', 'Registo eliminado.');
    }
}