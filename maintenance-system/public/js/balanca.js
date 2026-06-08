/**
 * balanca.js — Leitura de balança industrial via Web Serial API
 * Compatível com: Chrome/Edge + adaptador RS-232/USB
 * Formatos suportados: Toledo, Filizola, Mettler, Sartorius, genérico
 */

const Balanca = (() => {

    let port     = null;
    let reader   = null;
    let _timeout = null;

    // ── Parsers por formato de trama ──────────────────────────────
    const parsers = [
        // Toledo/Mettler: "ST,GS,+  00012345.60 kg"
        {
            name: 'Toledo/Mettler',
            test: s => /ST,\s*(GS|NT)/.test(s),
            parse: s => {
                const m = s.match(/[+-]?\s*([\d]+\.?\d*)\s*kg/i);
                return m ? parseFloat(m[1]) : null;
            }
        },
        // Filizola: "P 000012345"
        {
            name: 'Filizola',
            test: s => /^P\s+\d/.test(s.trim()),
            parse: s => {
                const m = s.match(/P\s+([\d]+\.?\d*)/);
                return m ? parseFloat(m[1]) : null;
            }
        },
        // Sartorius: "     12345.600 g" ou "    12.345 kg"
        {
            name: 'Sartorius',
            test: s => /^\s+[\d\s]+[,.]?\d*\s*(g|kg)\s*$/.test(s),
            parse: s => {
                const m  = s.match(/([\d]+[.,]?\d*)\s*(g|kg)/i);
                if (!m) return null;
                const v = parseFloat(m[1].replace(',', '.'));
                return m[2].toLowerCase() === 'g' ? v / 1000 : v;
            }
        },
        // Genérico: qualquer número seguido de kg/g, ou só número
        {
            name: 'Genérico',
            test: () => true,
            parse: s => {
                // Tenta "número kg"
                let m = s.match(/([\d]+[.,]?\d*)\s*kg/i);
                if (m) return parseFloat(m[1].replace(',', '.'));
                // Tenta "número g" → converte para kg
                m = s.match(/([\d]+[.,]?\d*)\s*g\b/i);
                if (m) return parseFloat(m[1].replace(',', '.')) / 1000;
                // Tenta só número isolado (últimos dígitos da trama)
                m = s.replace(/[^0-9.,]/g, ' ').trim().match(/([\d]+[.,]\d+|[\d]{4,})/);
                if (m) return parseFloat(m[1].replace(',', '.'));
                return null;
            }
        }
    ];

    function detectarFormato(trama) {
        for (const p of parsers) {
            if (p.test(trama)) return p;
        }
        return parsers[parsers.length - 1]; // fallback genérico
    }

    // ── API pública ───────────────────────────────────────────────

    /**
     * Lê o peso da balança uma vez.
     * @param {object} opts - { baudRate, dataBits, stopBits, parity, onStatus }
     * @returns {Promise<number>} peso em kg
     */
    async function lerPeso(opts = {}) {
        const config = {
            baudRate : opts.baudRate  || 9600,
            dataBits : opts.dataBits  || 8,
            stopBits : opts.stopBits  || 1,
            parity   : opts.parity    || 'none',
        };
        const onStatus = opts.onStatus || (() => {});

        if (!('serial' in navigator)) {
            throw new Error('Web Serial API não suportada. Usa Chrome ou Edge.');
        }

        onStatus('Escolhe a porta COM da balança...');

        // Deixa o utilizador escolher a porta (lembra depois de "Autorizar sempre")
        port = await navigator.serial.requestPort();

        onStatus('A ligar à balança...');
        await port.open(config);

        onStatus('À espera de dados da balança...');

        return new Promise((resolve, reject) => {
            let buffer = '';

            // Timeout de 10 segundos
            _timeout = setTimeout(async () => {
                await _fechar();
                reject(new Error('Tempo esgotado — a balança não respondeu em 10s.\nVerifica se está ligada e estável.'));
            }, 10000);

            (async () => {
                reader = port.readable.getReader();
                try {
                    while (true) {
                        const { value, done } = await reader.read();
                        if (done) break;

                        buffer += new TextDecoder().decode(value);

                        // Processa quando recebe fim de linha
                        if (buffer.includes('\n') || buffer.includes('\r')) {
                            const linhas = buffer.split(/[\r\n]+/).filter(l => l.trim());

                            for (const linha of linhas) {
                                const fmt   = detectarFormato(linha);
                                const peso  = fmt.parse(linha);

                                if (peso !== null && peso > 0) {
                                    clearTimeout(_timeout);
                                    onStatus(`Lido (${fmt.name}): ${peso} kg`);
                                    await _fechar();
                                    resolve(peso);
                                    return;
                                }
                            }
                            buffer = '';
                        }
                    }
                } catch (e) {
                    clearTimeout(_timeout);
                    await _fechar();
                    // Ignorar cancelamentos propositais
                    if (e.name !== 'AbortError') reject(e);
                }
            })();
        });
    }

    async function _fechar() {
        try {
            if (reader) { await reader.cancel(); reader.releaseLock(); reader = null; }
            if (port)   { await port.close(); port = null; }
        } catch (_) {}
    }

    async function cancelar() {
        clearTimeout(_timeout);
        await _fechar();
    }

    return { lerPeso, cancelar };
})();