<?php

return [
    // Aviso na 2ª pesagem: líquido (kg) vs sacos × peso do saco
    'peso_saco_kg'   => 50,   // ajuste ao peso real do saco
    'tolerancia_pct' => 3,    // diferença aceite antes de avisar (%)

    // Leitura da balança (Web Serial)
    'balanca' => [
        'baud'      => 9600,
        'espera_ms' => 1500,
    ],
];
