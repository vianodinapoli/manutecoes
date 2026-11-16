@props([
    'segundos' => 30,
    'alvo' => '#conteudo-atualizavel',
    'verificar' => null,
    'verificarSegundos' => 2,
])

<script>
    (function () {
        const intervaloMs = {{ (int) $segundos }} * 1000;
        const seletor = @js($alvo);
        const urlSinal = @js($verificar);
        const sinalMs = {{ (int) $verificarSegundos }} * 1000;
        const TIMEOUT_MS = 15000;
        let ocupado = false;
        let ultimoSinal = null;

        function bloqueado() {
            if (document.hidden) return true;
            const atual = document.querySelector(seletor);
            if (!atual) return true;
            const ativo = document.activeElement;
            if (ativo && atual.contains(ativo) && ['INPUT', 'TEXTAREA', 'SELECT'].includes(ativo.tagName)) return true;
            if (document.querySelector('.modal.show, [data-no-refresh]:not([hidden])')) return true;
            return false;
        }

        async function pedir(url, tipo) {
            const controlador = new AbortController();
            const timer = setTimeout(() => controlador.abort(), TIMEOUT_MS);
            try {
                const resposta = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': tipo === 'json' ? 'application/json' : 'text/html' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                    signal: controlador.signal,
                });
                if (resposta.status === 401 || resposta.status === 419 ||
                    (resposta.redirected && /login/i.test(resposta.url))) {
                    window.location.reload();
                    return null;
                }
                if (!resposta.ok) {
                    console.warn('auto-refresh: resposta', resposta.status);
                    return null;
                }
                return tipo === 'json' ? await resposta.json() : await resposta.text();
            } finally {
                clearTimeout(timer);
            }
        }

        async function atualizar() {
            if (ocupado || bloqueado()) return false;
            ocupado = true;
            try {
                const html = await pedir(window.location.href, 'html');
                if (html === null) return false;

                const doc = new DOMParser().parseFromString(html, 'text/html');
                const novo = doc.querySelector(seletor);
                const destino = document.querySelector(seletor);
                if (destino && novo) {
                    if (destino.innerHTML !== novo.innerHTML) {
                        destino.innerHTML = novo.innerHTML;
                        document.dispatchEvent(new CustomEvent('auto-refresh:feito'));
                    }
                    return true;
                }
                console.warn('auto-refresh: alvo não encontrado na resposta');
                return false;
            } catch (e) {
                console.warn('auto-refresh falhou', e);
                return false;
            } finally {
                ocupado = false;
            }
        }

        async function verificarMudanca() {
            if (ocupado || document.hidden) return;
            try {
                const dados = await pedir(urlSinal, 'json');
                if (!dados) return;
                if (ultimoSinal === null) { ultimoSinal = dados.s; return; }
                if (dados.s !== ultimoSinal) {
                    const ok = await atualizar();
                    if (ok) ultimoSinal = dados.s; // se foi bloqueado, tenta de novo no próximo ciclo
                }
            } catch (e) {
                console.warn('auto-refresh (sinal) falhou', e);
            }
        }

        if (urlSinal) {
            setInterval(verificarMudanca, sinalMs);
            setInterval(atualizar, intervaloMs); // rede de segurança
        } else {
            setInterval(atualizar, intervaloMs);
        }

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) { urlSinal ? verificarMudanca() : atualizar(); }
        });
    })();
</script>