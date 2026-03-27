<?php

namespace App\Http\Controllers;

use App\Models\Viatura;
use App\Models\ViaturaDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViaturaController extends Controller
{
    // ─── INDEX ───────────────────────────────────────
    public function index()
    {
        $viaturas = Viatura::with('documentos')->orderBy('created_at', 'desc')->get();

        // A view usa @json($v) no onclick — os documentos precisam
        // de estar no objecto serializado
        return view('viaturas.index', compact('viaturas'));
    }

    // ─── STORE ───────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'marca'        => 'required|string|max:100',
            'modelo'       => 'nullable|string|max:100',
            'matricula'    => 'required|string|max:30|unique:viaturas,matricula',
            'numero_chassi'=> 'nullable|string|max:100',
            'cor'          => 'nullable|string|max:50',
            'seguro'       => 'required|date',
            'ipo'          => 'required|date',
            'observacoes'  => 'nullable|string',
            'metadata'     => 'nullable|string', // JSON string
            'documentos.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        // Tratar metadata (vem como JSON string do hidden input)
        $data['metadata'] = $this->parseMetadata($request->input('metadata'));

        $viatura = Viatura::create($data);

        $this->guardarDocumentos($request, $viatura);

        return redirect()->route('viaturas.index')
                         ->with('success', 'Viatura registada com sucesso.');
    }

    // ─── UPDATE ──────────────────────────────────────
    public function update(Request $request, Viatura $viatura)
    {
        $data = $request->validate([
            'marca'        => 'required|string|max:100',
            'modelo'       => 'nullable|string|max:100',
            'matricula'    => 'required|string|max:30|unique:viaturas,matricula,' . $viatura->id,
            'numero_chassi'=> 'nullable|string|max:100',
            'cor'          => 'nullable|string|max:50',
            'seguro'       => 'required|date',
            'ipo'          => 'required|date',
            'observacoes'  => 'nullable|string',
            'metadata'     => 'nullable|string',
            'documentos.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        $data['metadata'] = $this->parseMetadata($request->input('metadata'));

        $viatura->update($data);

        $this->guardarDocumentos($request, $viatura);

        return redirect()->route('viaturas.index')
                         ->with('success', 'Viatura actualizada com sucesso.');
    }

    // ─── DESTROY ─────────────────────────────────────
    public function destroy(Viatura $viatura)
    {
        // Apagar ficheiros do disco
        foreach ($viatura->documentos as $doc) {
            Storage::disk('public')->delete($doc->path);
        }

        $viatura->delete(); // cascade apaga registos de documentos

        return redirect()->route('viaturas.index')
                         ->with('success', 'Viatura eliminada com sucesso.');
    }

    // ─── ELIMINAR DOCUMENTO INDIVIDUAL ───────────────
    public function destroyDocumento(ViaturaDocumento $documento)
    {
        Storage::disk('public')->delete($documento->path);
        $documento->delete();

        return response()->json(['success' => true]);
    }

    // ─── EXPORT CSV ──────────────────────────────────
    public function export(): StreamedResponse
    {
        $viaturas = Viatura::with('documentos')->orderBy('marca')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="viaturas_' . now()->format('Ymd_His') . '.csv"',
        ];

        return response()->stream(function () use ($viaturas) {
            $out = fopen('php://output', 'w');
            // BOM para Excel reconhecer UTF-8
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['Marca','Modelo','Matrícula','Nº Chassi','Cor','Seguro','IPO','Observações'], ';');

            foreach ($viaturas as $v) {
                fputcsv($out, [
                    $v->marca,
                    $v->modelo,
                    $v->matricula,
                    $v->numero_chassi,
                    $v->cor,
                    $v->seguro?->format('d/m/Y'),
                    $v->ipo?->format('d/m/Y'),
                    $v->observacoes,
                ], ';');
            }

            fclose($out);
        }, 200, $headers);
    }

    // ─── HELPERS PRIVADOS ────────────────────────────
    private function guardarDocumentos(Request $request, Viatura $viatura): void
    {
        if (!$request->hasFile('documentos')) return;

        foreach ($request->file('documentos') as $file) {
            $path = $file->store("viaturas/{$viatura->id}", 'public');

            $viatura->documentos()->create([
                'nome'    => $file->getClientOriginalName(),
                'path'    => $path,
                'mime'    => $file->getMimeType(),
                'tamanho' => $file->getSize(),
            ]);
        }
    }

    private function parseMetadata(?string $raw): array
    {
        if (!$raw) return [];
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}