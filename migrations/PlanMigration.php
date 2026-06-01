<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../core/Connection.php';
$config = require_once __DIR__ . '/../config/database.php';

use Migrartabela\Payloads\PlanoPayload;

// 🔌 Criando as conexões de origem e destino
$pdoOrigem = Connection::connect($config['databaseOld']);
$pdoDestino = Connection::connect($config['databaseNew']);

$sqlOrigem = "SELECT id, plano, valor
FROM contrato
GROUP BY plano, valor";
$stmtOrigem = $pdoOrigem->query($sqlOrigem);

$sqlDestino = "INSERT INTO plano (
plano, valor) 
VALUES (
:plano, :valor
)";
$stmtDestino = $pdoDestino->prepare($sqlDestino);

$totalMigrado = 0;

//fetch(PDO::FETCH_ASSOC)) retorna um array associativo, exemplo -> plano => ouro, valor => 100.
while ($dadosOrigem = $stmtOrigem->fetch(PDO::FETCH_ASSOC)) {

    $payloadPlano = PlanoPayload::criar([
        'Plano'      => $dadosOrigem['plano'],
        'Valor'      => $dadosOrigem['valor'],
        'externo_id' => $dadosOrigem['id'],
    ]);

    $colunas      = implode(', ', array_keys($payloadPlano));
    $placeholders = ':' . implode(', :', array_keys($payloadPlano));

    $stmtDestino = $pdoDestino->prepare("INSERT INTO plano ($colunas) VALUES ($placeholders)");
    $stmtDestino->execute($payloadPlano);

    $totalMigrado++;
}

    echo "✅ Migração concluída com sucesso! Total de registros: {$totalMigrado}\n";