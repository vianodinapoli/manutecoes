<div id="cartaNovaBox" style="border:1px solid #e9ecef;border-radius:10px;padding:14px">
  <div style="font-weight:700;margin-bottom:6px;font-size:.85rem">Carta nova (lista de camiões para o tablet)</div>

  <div id="cartaNovaAviso" style="display:none;font-size:13px;color:#6c757d">
    Guarde primeiro a operação. Depois volte a abri-la para carregar a lista de camiões.
  </div>

  <div id="cartaNovaCorpo" style="display:none">
    <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px">
      <input type="file" id="cartaNovaFicheiro" accept=".xlsx,.xls" class="form-control form-control-sm" style="flex:1">
      <button type="button" id="cartaNovaBtn" onclick="cartaNovaCarregar()" class="btn btn-primary btn-sm">
        Carregar
      </button>
    </div>
    <div id="cartaNovaMsg" style="font-size:13px;margin-bottom:8px"></div>
    <div id="cartaNovaLista"></div>
  </div>
</div>

<script>
(function () {
  const base = @json(url('operations'));
  const csrf = @json(csrf_token());
  let opId = null;

  const $ = (id) => document.getElementById(id);
  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  function msg(texto, erro) {
    const el = $('cartaNovaMsg');
    el.textContent = texto || '';
    el.style.color = erro ? '#b02a37' : '#198754';
  }

  async function listar() {
    const box = $('cartaNovaLista');
    box.innerHTML = '<span style="font-size:13px;color:#6c757d">A carregar…</span>';
    try {
      const r = await fetch(base + '/' + opId + '/carta-nova', { headers: { Accept: 'application/json' } });
      const d = await r.json();
      if (!d.cartas.length) {
        box.innerHTML = '<span style="font-size:13px;color:#6c757d">Ainda não há lista carregada. O tablet não vai alertar nada.</span>';
        return;
      }
      box.innerHTML = d.cartas.map((c) => `
        <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid #eee;padding:6px 0;font-size:14px">
          <span><b>${esc(c.do_numero)}</b> — ${c.total_camioes} camiões
            <small style="color:#6c757d">(${esc(c.ficheiro)})</small></span>
          <button type="button" onclick="cartaNovaRemover(${c.id}, '${esc(c.do_numero)}')"
                  class="btn btn-outline-danger btn-sm">Remover</button>
        </div>`).join('');
    } catch (e) {
      box.innerHTML = '<span style="color:#b02a37;font-size:13px">Não consegui obter as listas.</span>';
    }
  }

  window.cartaNovaAbrir = function (id) {
    opId = id || null;
    $('cartaNovaAviso').style.display = opId ? 'none' : 'block';
    $('cartaNovaCorpo').style.display = opId ? 'block' : 'none';
    if (!opId) return;
    $('cartaNovaFicheiro').value = '';
    msg('');
    listar();
  };

  window.cartaNovaCarregar = async function () {
    const f = $('cartaNovaFicheiro').files[0];
    if (!f) { msg('Escolha o ficheiro Excel da carta nova.', true); return; }

    const btn = $('cartaNovaBtn');
    btn.disabled = true; msg('A carregar…');
    const fd = new FormData();
    fd.append('file', f);

    try {
      const r = await fetch(base + '/' + opId + '/carta-nova', {
        method: 'POST',
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
        body: fd,
      });
      const d = await r.json().catch(() => ({}));
      if (!r.ok) {
        msg((d.errors && d.errors.file && d.errors.file[0]) || d.message || 'Falha ao carregar a lista.', true);
      } else {
        msg(d.message, false);
        $('cartaNovaFicheiro').value = '';
        listar();
      }
    } catch (e) {
      msg('Sem ligação ao servidor.', true);
    } finally {
      btn.disabled = false;
    }
  };

  window.cartaNovaRemover = async function (id, doNumero) {
    if (!confirm('Remover a lista ' + doNumero + '?')) return;
    const r = await fetch(base + '/' + opId + '/carta-nova/' + id, {
      method: 'DELETE',
      headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
    });
    const d = await r.json().catch(() => ({}));
    msg(d.message || (r.ok ? 'Lista removida.' : 'Falha ao remover.'), !r.ok);
    listar();
  };
})();
</script>
