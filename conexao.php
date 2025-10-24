<?php

    //conexao de origem

define('HOST', '127.0.0.1');
define('USUARIO','root');
define('SENHA','hynand1298');
define('DB', 'tabelaveiculos');
    $conexaoOrigem = mysqli_connect(HOST, USUARIO, SENHA, DB) or die('Não foi possível conectar');

    //conexao de destino
define('HOST', '127.0.0.1');
define('USUARIO','root');
define('SENHA','hynand1298');
define('DB', 'tabelaveiculos2');
    $conexaoOrigem = mysqli_connect(HOST, USUARIO, SENHA, DB) or die('Não foi possível conectar');

?>