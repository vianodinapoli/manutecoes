<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    private function dbPath(): string
    {
        return database_path(basename(config('database.connections.sqlite.database')));
    }

    public function index()
    {
        return view('admin.backup.index');
    }

    public function download()
    {
        $dbPath = $this->dbPath();

        if (!file_exists($dbPath)) {
            return back()->with('error', 'Ficheiro de base de dados não encontrado.');
        }

        $filename = 'backup_fem_' . now()->format('Y-m-d_H-i-s') . '.sqlite';

        return response()->download($dbPath, $filename, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sqlite,db|max:102400', // 100MB max
        ]);

        $dbPath = $this->dbPath();

        // Guardar cópia de segurança automática antes de restaurar
        $autoBackup = $dbPath . '.before_restore_' . now()->format('YmdHis') . '.bak';
        copy($dbPath, $autoBackup);

        try {
            $request->file('backup_file')->move(
                dirname($dbPath),
                basename($dbPath)
            );

            return back()->with('success', 'Base de dados restaurada com sucesso! Uma cópia automática foi guardada no servidor antes de restaurar.');
        } catch (\Exception $e) {
            // Reverter se falhar
            copy($autoBackup, $dbPath);
            return back()->with('error', 'Erro ao restaurar: ' . $e->getMessage());
        }
    }
}