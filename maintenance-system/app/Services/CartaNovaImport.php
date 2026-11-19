<?php

namespace App\Services;

use App\Models\CartaNova;
use App\Models\CartaNovaCamiao;
use App\Models\DischargeOperation;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

/**
 * Importa o Excel "CARTA_NOVA_CommTrac" (folha "Pre-Advised Trucks").
 *
 * Estrutura esperada:
 *   - algures nas primeiras linhas: "Delivery/Intake Order Number" | DO-0004785
 *   - linha de cabeçalhos: Truck License Plate | ... | Driver 1 First Name | Driver 1 Last Name | Driver 1 License Number
 *   - a seguir, uma linha por camião
 *
 * As colunas são encontradas pelo NOME do cabeçalho (não pela posição),
 * por isso continua a funcionar se acrescentarem ou moverem colunas.
 */
class CartaNovaImport
{
    private const FOLHA = 'Pre-Advised Trucks';

    private const COLUNAS = [
        'matricula'      => 'truck license plate',
        'primeiro_nome'  => 'driver 1 first name',
        'ultimo_nome'    => 'driver 1 last name',
        'carta_conducao' => 'driver 1 license number',
    ];

    /**
     * @return array{carta: CartaNova, do_numero: string, linhas: int, importados: int, duplicados: int, ignorados: int, substituiu: bool}
     * @throws RuntimeException com mensagem pronta para mostrar ao utilizador
     */
    public function importar(DischargeOperation $operation, string $caminho, ?string $nomeFicheiro, ?int $userId): array
    {
        $reader = IOFactory::createReaderForFile($caminho);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($caminho);

        $folha = $spreadsheet->getSheetByName(self::FOLHA) ?? $spreadsheet->getSheet(0);
        $linhas = $folha->toArray(null, true, false, false); // valores crus, índices 0..n

        $doNumero = $this->encontrarDo($linhas);
        if ($doNumero === null) {
            throw new RuntimeException('Não encontrei o "Delivery/Intake Order Number" (número do DO) no Excel.');
        }

        [$linhaCabecalho, $colunas] = $this->encontrarCabecalho($linhas);

        $camioes = [];
        $vistos = [];
        $total = 0;
        $duplicados = 0;
        $ignorados = 0;

        for ($i = $linhaCabecalho + 1, $n = count($linhas); $i < $n; $i++) {
            $linha = $linhas[$i];

            $matricula = CartaNovaCamiao::normalizarMatricula($this->texto($linha[$colunas['matricula']] ?? null));
            if ($matricula === '') {
                continue; // linha vazia
            }
            $total++;

            $primeiro = $this->nome($linha[$colunas['primeiro_nome']] ?? null);
            $ultimo   = $this->nome($linha[$colunas['ultimo_nome']] ?? null);
            $cartaCond = $this->texto($linha[$colunas['carta_conducao']] ?? null);

            if ($primeiro === '' || $cartaCond === '') {
                $ignorados++;
                continue;
            }

            // Mesma matrícula + mesma carta = linha repetida. Mesma matrícula com
            // motorista diferente é válido e fica.
            $chave = $matricula . '|' . preg_replace('/\s+/', '', $cartaCond);
            if (isset($vistos[$chave])) {
                $duplicados++;
                continue;
            }
            $vistos[$chave] = true;

            $camioes[] = [
                'matricula'      => $matricula,
                'primeiro_nome'  => $primeiro,
                'ultimo_nome'    => $ultimo,
                'carta_conducao' => $cartaCond,
            ];
        }

        if (empty($camioes)) {
            throw new RuntimeException('O Excel não tem nenhum camião válido (matrícula, nome e carta de condução).');
        }

        $substituiu = false;

        $carta = DB::transaction(function () use ($operation, $doNumero, $nomeFicheiro, $userId, $camioes, &$substituiu) {
            $existente = CartaNova::where('operation_id', $operation->id)
                ->where('do_numero', $doNumero)
                ->first();

            $substituiu = $existente !== null;

            $carta = $existente ?? new CartaNova([
                'operation_id' => $operation->id,
                'do_numero'    => $doNumero,
            ]);
            $carta->fill([
                'ficheiro'      => $nomeFicheiro,
                'total_camioes' => count($camioes),
                'uploaded_by'   => $userId,
            ])->save();

            // Substitui a lista anterior deste DO
            $carta->camioes()->delete();

            $agora = now();
            foreach (array_chunk($camioes, 200) as $bloco) {
                CartaNovaCamiao::insert(array_map(fn ($c) => $c + [
                    'carta_nova_id' => $carta->id,
                    'created_at'    => $agora,
                    'updated_at'    => $agora,
                ], $bloco));
            }

            return $carta;
        });

        return [
            'carta'      => $carta,
            'do_numero'  => $doNumero,
            'linhas'     => $total,
            'importados' => count($camioes),
            'duplicados' => $duplicados,
            'ignorados'  => $ignorados,
            'substituiu' => $substituiu,
        ];
    }

    /** Procura "Delivery/Intake Order Number" nas primeiras linhas e devolve a célula seguinte. */
    private function encontrarDo(array $linhas): ?string
    {
        foreach (array_slice($linhas, 0, 10) as $linha) {
            foreach ($linha as $col => $valor) {
                if (preg_match('/delivery.*order\s*number/i', $this->texto($valor))) {
                    for ($j = $col + 1; $j < count($linha); $j++) {
                        $do = mb_strtoupper($this->texto($linha[$j]));
                        if ($do !== '') {
                            return $do;
                        }
                    }
                }
            }
        }
        return null;
    }

    /** @return array{0:int, 1:array<string,int>} índice da linha de cabeçalhos e mapa campo → coluna */
    private function encontrarCabecalho(array $linhas): array
    {
        foreach (array_slice($linhas, 0, 15, true) as $i => $linha) {
            $mapa = [];
            foreach ($linha as $col => $valor) {
                $nome = mb_strtolower(preg_replace('/\s+/', ' ', $this->texto($valor)));
                foreach (self::COLUNAS as $campo => $cabecalho) {
                    if ($nome === $cabecalho) {
                        $mapa[$campo] = $col;
                    }
                }
            }
            if (count($mapa) === count(self::COLUNAS)) {
                return [$i, $mapa];
            }
        }

        throw new RuntimeException(
            'Não encontrei os cabeçalhos esperados: "Truck License Plate", "Driver 1 First Name", '
            . '"Driver 1 Last Name" e "Driver 1 License Number".'
        );
    }

    /** Converte qualquer valor de célula em texto limpo (números inteiros sem ".0"). */
    private function texto(mixed $valor): string
    {
        if ($valor === null) {
            return '';
        }
        if (is_float($valor) && floor($valor) === $valor) {
            $valor = (int) $valor;
        }
        return trim((string) $valor);
    }

    /** Nome em maiúsculas, sem espaços a mais ("Raul  " → "RAUL"). */
    private function nome(mixed $valor): string
    {
        return mb_strtoupper(preg_replace('/\s+/', ' ', $this->texto($valor)));
    }
}
