<?php

if (file_exists('config_local.php')) {
    require_once 'config_local.php';
} else {
    require_once 'conexao.php';
}

$sqlSelect = "SELECT * FROM veiculos"; 
$resultadoOrigem = $conexaoDbOrigem->query($sqlSelect);

if (!$resultadoOrigem) {
    echo "ERRO FATAL NA LEITURA: " . mysqli_error($conexaoDbOrigem) . ";";
}

$tabela_destino = 'tabelaveiculos2.veiculos'; 
$registrosProcessados = 0;
$todasAsLinhas = []; 

if (mysqli_num_rows($resultadoOrigem) > 0) {

    while ($linha = $resultadoOrigem->fetch_assoc()) {
        
        $id_origem = $linha['id']; 
        $nome = $linha['nome'];
        $modelo = $linha['modelo'];
        
        $todasAsLinhas[] = $linha; 
        
   
        $sqlInsert = "
            INSERT INTO {$tabela_destino} (nome, modelo, id_origem) 
            VALUES (
                '{$nome}', 
                '{$modelo}',
                {$id_origem}
            )";

        if (mysqli_query($conexaoDbDestino, $sqlInsert)) {
            $registrosProcessados++;
            echo "RESULTADO: SUCESSO. Linhas afetadas: " . mysqli_affected_rows($conexaoDbDestino) . ";";
        }
        else {
           
            echo "ERRO FATAL DURANTE A MIGRAÇÃO (ID Original {$id_origem}): " . mysqli_error($conexaoDbDestino) . ");";
        }
    } 
} else {
    echo "AVISO: Nenhuma linha encontrada no banco de origem.<br>";
}

mysqli_close($conexaoDbOrigem);
mysqli_close($conexaoDbDestino);

echo "Total de registros processados (inseridos): " . $registrosProcessados . "<br>";

// Exibição dos dados lidos
$id_array = array_column($todasAsLinhas, 'id');
$nomes_array = array_column($todasAsLinhas, 'nome');
$modelos_array = array_column($todasAsLinhas, 'modelo');

echo '<pre>';
echo "IDs Originais: ";
print_r($id_array);
echo "Nomes: ";
print_r($nomes_array); 
echo "Modelos: ";
print_r($modelos_array);
echo '</pre>';

?>