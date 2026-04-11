<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    *, *::before, *::after { box-sizing: border-box; }
    .backup-wrap { max-width: 760px; margin: 0 auto; }
    .page-label { font-size:.58rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#94a3b8; margin-bottom:3px; }
    .page-title { font-size:1.25rem; font-weight:800; color:#1e293b; margin:0 0 2px; }
    .page-sub   { font-size:.75rem; color:#94a3b8; margin:0 0 28px; }

    .card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 1px 4px rgba(0,0,0,.04); overflow:hidden; margin-bottom:20px; }
    .card-head { padding:18px 22px 14px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:12px; }
    .card-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; }
    .card-icon.green  { background:#f0fdf4; color:#16a34a; }
    .card-icon.blue   { background:#eff6ff; color:#2563eb; }
    .card-icon.orange { background:#fff7ed; color:#ea580c; }
    .card-title { font-size:.9rem; font-weight:700; color:#1e293b; }
    .card-desc  { font-size:.72rem; color:#94a3b8; margin-top:2px; }
    .card-body  { padding:20px 22px; }

    .info-row { display:flex; align-items:center; gap:10px; padding:10px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:14px; }
    .info-row i { color:#6366f1; font-size:.9rem; }
    .info-row span { font-size:.8rem; color:#475569; }
    .info-row strong { color:#1e293b; }

    .btn-download { display:inline-flex; align-items:center; gap:8px; background:#1e293b; color:#fff; border:none; border-radius:9px; padding:10px 22px; font-size:.83rem; font-weight:700; cursor:pointer; text-decoration:none; transition:background .15s; }
    .btn-download:hover { background:#334155; color:#fff; }

    .drop-zone { border:2px dashed #cbd5e1; border-radius:12px; padding:32px 20px; text-align:center; cursor:pointer; transition:border-color .2s, background .2s; position:relative; }
    .drop-zone:hover, .drop-zone.dragover { border-color:#6366f1; background:#f5f3ff; }
    .drop-zone i { font-size:2rem; color:#cbd5e1; display:block; margin-bottom:10px; transition:color .2s; }
    .drop-zone:hover i, .drop-zone.dragover i { color:#6366f1; }
    .drop-zone-text { font-size:.82rem; color:#64748b; }
    .drop-zone-text strong { color:#1e293b; }
    .drop-zone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .file-chosen { margin-top:10px; font-size:.75rem; color:#6366f1; font-weight:600; display:none; }

    .warn-box { display:flex; gap:10px; background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:12px 16px; margin:16px 0 0; }
    .warn-box i { color:#ea580c; flex-shrink:0; margin-top:2px; }
    .warn-box p { font-size:.75rem; color:#9a3412; margin:0; line-height:1.5; }

    .btn-restore { display:inline-flex; align-items:center; gap:8px; background:#dc2626; color:#fff; border:none; border-radius:9px; padding:10px 22px; font-size:.83rem; font-weight:700; cursor:pointer; transition:background .15s; margin-top:16px; }
    .btn-restore:hover { background:#b91c1c; }
    .btn-restore:disabled { opacity:.5; cursor:not-allowed; }
</style>

<div class="container-fluid py-3 px-3 px-md-4">
<div class="backup-wrap">

    <div class="page-label">Administração</div>
    <h4 class="page-title">Backup da Base de Dados</h4>
    <p class="page-sub">Exporta ou restaura a base de dados SQLite do sistema</p>

    {{-- ── DOWNLOAD ── --}}
    <div class="card">
        <div class="card-head">
            <div class="card-icon green"><i class="bi bi-download"></i></div>
            <div>
                <div class="card-title">Exportar / Download</div>
                <div class="card-desc">Faz download do ficheiro .sqlite actual</div>
            </div>
        </div>
        <div class="card-body">
            <div class="info-row">
                <i class="bi bi-database-fill"></i>
                <span>Base de dados: <strong>{{ basename(config('database.connections.sqlite.database')) }}</strong></span>
                <span style="margin-left:auto;font-size:.72rem;color:#94a3b8;">
                    Tamanho: <strong>{{ file_exists(database_path(basename(config('database.connections.sqlite.database')))) ? number_format(filesize(database_path(basename(config('database.connections.sqlite.database')))) / 1024, 1) . ' KB' : 'N/D' }}</strong>
                </span>
            </div>
            <a href="{{ route('admin.backup.download') }}" class="btn-download">
                <i class="bi bi-download"></i> Descarregar Backup
            </a>
        </div>
    </div>

    {{-- ── RESTORE ── --}}
    <div class="card">
        <div class="card-head">
            <div class="card-icon orange"><i class="bi bi-upload"></i></div>
            <div>
                <div class="card-title">Restaurar Backup</div>
                <div class="card-desc">Substitui a base de dados actual por um ficheiro de backup</div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.backup.restore') }}" enctype="multipart/form-data"
                  onsubmit="return confirm('Tens a certeza? A base de dados actual será substituída. Uma cópia automática será guardada no servidor antes de restaurar.')">
                @csrf

                <div class="drop-zone" id="dropZone">
                    <input type="file" name="backup_file" id="backupFile" accept=".sqlite,.db"
                           onchange="mostrarFicheiro(this)">
                    <i class="bi bi-cloud-upload"></i>
                    <div class="drop-zone-text">
                        <strong>Clica para seleccionar</strong> ou arrasta aqui o ficheiro<br>
                        <span style="font-size:.7rem;color:#94a3b8;">Formatos aceites: .sqlite, .db — Máx. 100 MB</span>
                    </div>
                    <div class="file-chosen" id="fileChosen"><i class="bi bi-check-circle-fill"></i> <span id="fileName"></span></div>
                </div>

                <div class="warn-box">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <p>
                        <strong>Atenção:</strong> Restaurar um backup irá <strong>substituir todos os dados actuais</strong>.
                        Uma cópia de segurança automática é guardada no servidor antes de qualquer restauro.
                        Esta operação é irreversível via interface.
                    </p>
                </div>

                <button type="submit" class="btn-restore" id="btnRestore" disabled>
                    <i class="bi bi-arrow-counterclockwise"></i> Restaurar Base de Dados
                </button>
            </form>
        </div>
    </div>

</div>
</div>

<script>
    function mostrarFicheiro(input) {
        var chosen  = document.getElementById('fileChosen');
        var name    = document.getElementById('fileName');
        var btn     = document.getElementById('btnRestore');
        if (input.files && input.files[0]) {
            name.textContent = input.files[0].name;
            chosen.style.display = 'block';
            btn.disabled = false;
        }
    }

    // Drag & drop visual
    var zone = document.getElementById('dropZone');
    zone.addEventListener('dragover',  function(e){ e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', function(){ zone.classList.remove('dragover'); });
    zone.addEventListener('drop',      function(e){
        e.preventDefault();
        zone.classList.remove('dragover');
        var input = document.getElementById('backupFile');
        input.files = e.dataTransfer.files;
        mostrarFicheiro(input);
    });
</script>
</x-app-layout>