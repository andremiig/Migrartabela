<?php

class Connection
{
    public static function connect(array $config): PDO
    {
        return new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']}",
            $config['user'],
            $config['password']
        );
    }
}