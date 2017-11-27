<?php declare(strict_types=1);

namespace Epic\Repositories;

use PDO;

/**
 * Class Repository
 *
 * @doc :: http://php.net/manual/en/class.pdo.php
 */
abstract class Repository
{
    const USERNAME = 'root';
    const PASSWORD = '';
    const HOST = 'localhost';
    const DB = 'morpion';

    protected function getConnection()
    {
        $username = self::USERNAME;
        $password = self::PASSWORD;
        $host = self::HOST;
        $db = self::DB;

        return new PDO("mysql:dbname=$db;host=$host;charset=utf8;unix_socket=/run/mysqld/mysqld.sock", $username, $password);
    }
}
