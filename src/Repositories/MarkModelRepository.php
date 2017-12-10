<?php

namespace Epic\Repositories;

use Epic\Entities\MarkModel;

use PDO;

class MarkModelRepository extends Repository
{
    public function getAll()
    {
        $connection = $this->getConnection();

        $request = $connection->query('SELECT * FROM MarkModel');

        return $request->fetchAll(PDO::FETCH_CLASS, MarkModel::class);
    }
}
