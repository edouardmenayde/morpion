<?php declare(strict_types=1);

namespace Epic\Repositories;

class TeamRepository extends Repository
{
    public function insert($team)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('INSERT INTO Team (name, color, createdAt) VALUES (:name, :color, :createdAt)');

        $createdAt = date(DATE_ISO8601);

        $request->execute(array(
            ':name' => $team->name,
            ':color' => $team->color,
            ':createdAt' => $createdAt
        ));

        $team->createdAt = $createdAt;
        $team->id = $connection->lastInsertId();

        return $team;

    }
}
