<?php

if (file_exists('config_local.php')) {
    require_once 'config_local.php';
} else {
    require_once 'conexao.php';
}

echo "--- INÍCIO DA MIGRAÇÃO ---\n";

// Garante que a leitura da origem é ordenada
$sqlSelect = "SELECT * FROM veiculos ORDER BY id ASC"; 
$resultadoOrigem = $conexaoDbOrigem->query($sqlSelect);

// Verifica falha no SELECT e interrompe o script
if ($resultadoOrigem === false) {
    die("\nERRO FATAL NA LEITURA: " . mysqli_error($conexaoDbOrigem) . "\n");
}

$tabela_destino = 'tabelaveiculos2.veiculos'; 
$registrosProcessados = 0;
$todasAsLinhas = []; 

if (mysqli_num_rows($resultadoOrigem) > 0) {

    echo "Tabela de Destino: {$tabela_destino}<br>"; 

    while ($linha = $resultadoOrigem->fetch_assoc()) {
        
        $id = $linha['id']; 
        $nome = $linha['nome'];
        $modelo = $linha['modelo'];
        
        // Adiciona ao array de relatório
        $todasAsLinhas[] = $linha; 
        
        // 1. SEGURANÇA: Prepara as strings (MANTIDO)
        $nome_seguro = mysqli_real_escape_string($conexaoDbDestino, $nome);
        $modelo_seguro = mysqli_real_escape_string($conexaoDbDestino, $modelo);

        // 2. SOLUÇÃO CHAVE: Usando ON DUPLICATE KEY UPDATE
        $sqlInsertUpdate = "
            INSERT INTO {$tabela_destino} (id, nome, modelo) 
            VALUES (
                {$id},             
                '{$nome_seguro}', 
                '{$modelo_seguro}'
            )
            ON DUPLICATE KEY UPDATE
                nome = VALUES(nome),
                modelo = VALUES(modelo)";

        echo "Processando ID: {$id}.<br>";

        // 3. Executa a inserção/atualização
        if (mysqli_query($conexaoDbDestino, $sqlInsertUpdate)) {
            $registrosProcessados++;
            echo "RESULTADO: SUCESSO. (Linhas afetadas: " . mysqli_affected_rows($conexaoDbDestino) . ")<br>"; 
        }
        else {
            // Se o INSERT falhar, o script é interrompido aqui (die/exit)
            die("\nERRO FATAL DURANTE A MIGRAÇÃO (ID {$id}): " . mysqli_error($conexaoDbDestino) . "<br>");
        }
    } // Fim do while
} else {
    echo "AVISO: Nenhuma linha encontrada no banco de origem.<br>";
}


mysqli_close($conexaoDbOrigem);
mysqli_close($conexaoDbDestino);

echo "\n--- FIM DA MIGRAÇÃO ---<br>";
echo "Total de registros processados (inseridos ou atualizados): " . $registrosProcessados . "<br>";

// Exibição dos dados lidos
$id_array = array_column($todasAsLinhas, 'id');
$nomes_array = array_column($todasAsLinhas, 'nome');
$modelos_array = array_column($todasAsLinhas, 'modelo');

echo '<pre>';
echo "IDs: ";
print_r($id_array);
echo "Nomes: ";
print_r($nomes_array); 
echo "Modelos: ";
print_r($modelos_array);
echo '</pre>';

?>