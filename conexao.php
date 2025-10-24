<?php

    //conexao de origem

define('HOST', '127.0.0.1');
define('USUARIO','root');
define('SENHA','');
define('DB', 'tabelaveiculos');
    $conexaoOrigem = mysqli_connect(HOST, USUARIO, SENHA, DB) or die('Não foi possível conectar ao banco de origem');

    //conexao de destino
define('HOST', '127.0.0.1');
define('USUARIO','root');
define('SENHA','');
define('DB', 'tabelaveiculos2');
    $conexaoDestino = mysqli_connect(HOST, USUARIO, SENHA, DB) or die('Não foi possível conectar ao banco destino');

?>