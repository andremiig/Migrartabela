<?php


define('HOST_ORIGEM', '127.0.0.1');
define('USUARIO_ORIGEM','root');
define('SENHA_ORIGEM','');
define('DB_ORIGEM', 'tabelaveiculos');
    $conexaoDbOrigem = mysqli_connect(HOST_ORIGEM, USUARIO_ORIGEM, SENHA_ORIGEM, DB_ORIGEM) or die('Não foi possível conectar ao banco de origem');

    //conexao de destino
define('HOST_DESTINO', '127.0.0.1');
define('USUARIO_DESTINO','root');
define('SENHA_DESTINO','');
define('DB_DESTINO', 'tabelaveiculos2');
    $conexaoDbDestino = mysqli_connect(HOST_DESTINO, USUARIO_DESTINO, SENHA_DESTINO, DB_DESTINO) or die('Não foi possível conectar ao banco destino');

?>