<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../core/Connection.php';
$config = require_once __DIR__ . '/../config/database.php';

use Migrartabela\Payloads\PessoaPayload;
use Migrartabela\Sanitizer\PersonTreatment;

$pdoOrigem  = Connection::connect($config['databaseOld']);
$pdoDestino = Connection::connect($config['databaseNew']);

$sqlOrigem = "SELECT 
    p.id, p.nome, p.cpf_cnpj, p.data_nascimento, p.cidade, p.estado, p.setor,
    p.endereco, p.numero, p.complemento, p.email,
    pt.numero as telefone_encontrado
FROM pessoa p
LEFT JOIN pessoa_telefone pt ON pt.pessoa_id = p.id
ORDER BY p.id ASC, pt.updated_at DESC";

$stmtOrigem = $pdoOrigem->query($sqlOrigem);

$totalMigrado = 0;

while ($dadosOrigem = $stmtOrigem->fetch(PDO::FETCH_ASSOC)) {

   $payloadPessoa = PessoaPayload::criar([
    'Nome'           => PersonTreatment::nome($dadosOrigem['nome']),
    'Cpf'            => PersonTreatment::cpfCnpj($dadosOrigem['cpf_cnpj']),
    'DataNascimento' => PersonTreatment::dataNascimento($dadosOrigem['data_nascimento']),
    'Endereco'       => PersonTreatment::endereco($dadosOrigem['endereco']),
    'Numero'         => PersonTreatment::numero($dadosOrigem['numero']),
    'Complemento'    => PersonTreatment::complemento($dadosOrigem['complemento']),
    'Setor'          => PersonTreatment::setor($dadosOrigem['setor']),
    'Cidade'         => PersonTreatment::cidade($dadosOrigem['cidade']),
    'Estado'         => PersonTreatment::estado($dadosOrigem['estado']),
    'Telefone1'      => PersonTreatment::telefone($dadosOrigem['telefone1']),
    'Telefone2'      => PersonTreatment::telefone($dadosOrigem['telefone2']),
    'Email'          => PersonTreatment::email($dadosOrigem['email']),
    'externo_id'     => $dadosOrigem['id'],
]);

    $colunas      = implode(', ', array_keys($payloadPessoa));
    $placeholders = ':' . implode(', :', array_keys($payloadPessoa));

    $stmtDestino = $pdoDestino->prepare("INSERT INTO pessoa ($colunas) VALUES ($placeholders)");
    $stmtDestino->execute($payloadPessoa);

    $totalMigrado++;
}

echo "✅ Migração concluída com sucesso! Total de registros: {$totalMigrado}\n";