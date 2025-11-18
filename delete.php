<?php
if (file_exists('config_local.php')) {
    require_once 'config_local.php';
} else {
    require_once 'conexao.php';
}

$deleteSql = "DELETE FROM veiculos WHERE id_Origem IS NOT NULL";
if ($conexaoDbDestino->query($deleteSql)) {
    echo "Dados deletados com sucesso.";
} else {
    echo "Erro ao deletar dados" . $conexaoDbDestino->error;
}

$conexaoDbDestino->close();

?>