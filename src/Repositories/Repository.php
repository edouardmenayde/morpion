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

    private $config;

    public function __construct()
    {
        $this->config = include(__DIR__ . '/../../config/database.php');
    }

    protected function getConnection()
    {
        return new PDO($this->config['dsn'], $this->config['username'], $this->config['password']);
    }
}
