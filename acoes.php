<?php
session_start();
require 'conexao.php';

$tabela = 'veiculos';
$sqlSelect = "SELECT nome, modelo FROM {$tabela}";
$registrosMigrados = 0;

try {
    // A. LEITURA (READ) no Banco de Origem
    $resultadoOrigem = mysqli_query($conexaoDbOrigem, $sqlSelect);

    if (mysqli_num_rows($resultadoOrigem) > 0) {


        $dados_para_migrar = mysqli_fetch_all($resultadoOrigem, MYSQLI_ASSOC);
        ///////////
        // Libera o resultado (boa prática, pois os dados estão no array)
        mysqli_free_result($resultadoOrigem);
        
        // B. PREPARAÇÃO para a Escrita
        $sqlInsert = "INSERT INTO {$tabela} (nome, modelo) VALUES (?, ?)";
        $stmtDestino = mysqli_prepare($conexaoDbDestino, $sqlInsert);

        // C. EXECUÇÃO DA ESCRITA (WRITE) no Banco de Destino usando FOREACH
        foreach ($dados_para_migrar as $linha) {
            
            // $linha agora contém o array associativo do registro atual
            
            // Faz o "bind" dos valores da linha atual
            // Os tipos (ss) representam 'string' e 'string'
            mysqli_stmt_bind_param($stmtDestino, 'ss', $linha['nome'], $linha['modelo']);
            
            // Executa a inserção
            mysqli_stmt_execute($stmtDestino);

            $registrosMigrados++;
        }

        echo "\nSucesso! {$registrosMigrados} registros migrados para o banco 'DESTINO'.\n";

        // Fecha o statement preparado
        mysqli_stmt_close($stmtDestino);

    } else {
        echo "\nO Banco de Origem não contém dados na tabela '{$tabela}'.\n";
    }

} catch (Exception $e) {
    echo "\nERRO DURANTE A MIGRAÇÃO: " . $e->getMessage() . "\n";
}

// ... (Fechamento das conexões) ...
?>