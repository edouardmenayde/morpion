<?php

namespace Epic\Repositories;

use PDO;

/**
 * Class Repository
 *
 * @doc :: http://php.net/manual/en/class.pdo.php
 */
abstract class Repository
{

    private $config;

    public function __construct()
    {
        $this->config = include(__DIR__ . '/../../config/database.php');
    }

    protected function getConnection()
    {
        $pdo = new PDO($this->config['dsn'], $this->config['username'], $this->config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
