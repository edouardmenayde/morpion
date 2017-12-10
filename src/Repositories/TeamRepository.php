<?php

namespace Epic\Repositories;

class TeamRepository extends Repository
{
    public function insert($team)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('INSERT INTO Team (name, color) VALUES (:name, :color)');

        $request->execute(array(
            ':name' => $team->name,
            ':color' => $team->color
        ));

        $team->id = $connection->lastInsertId();

        return $team;
    }
}
