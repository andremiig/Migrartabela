<?php

require_once 'conexao.php';

$tabela = 'veiculos';
$sqlSelect = "SELECT id, nome, modelo FROM {$tabela}";
$registrosMigrados = 0;

try {
    //conecta ao banco origem
    $resultadoOrigem = mysqli_query($conexaoDbOrigem, $sqlSelect);

    if (mysqli_num_rows($resultadoOrigem) > 0) {

       //cria um array com mysqli_fetch_all
        $dados_para_migrar = mysqli_fetch_all($resultadoOrigem, MYSQLI_ASSOC);
    
        
        // Prepara a inserção
        $sqlInsert = "INSERT INTO {$tabela} (nome, modelo) VALUES (?, ?)";
        $stmtDestino = mysqli_prepare($conexaoDbDestino, $sqlInsert);

        // C. Percorre linha por linha com foreach
        foreach ($dados_para_migrar as $linha) {
                    
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

echo(implode($dados_para_migrar)); 
// ... (Fechamento das conexões) ...
?>


