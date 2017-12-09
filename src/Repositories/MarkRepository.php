<?php

namespace Epic\Repositories;

use Epic\Entities\Mark;
use PDO;

class MarkRepository extends Repository
{
    public function insertAdvanced(Mark $mark)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('INSERT INTO Mark (damage, hp, mana, doubleAttack, teamId, markModelId) VALUES (:damage, :hp, :mana, :doubleAttack, :teamId, :markModelId)');

        $request->execute(array(
            ':damage' => $mark->damage,
            ':hp' => $mark->hp,
            ':mana' => $mark->mana,
            ':doubleAttack' => $mark->doubleAttack,
            ':teamId' => $mark->teamId,
            ':markModelId' => $mark->markModelId
        ));


        $mark->id = $connection->lastInsertId();

        return $mark;
    }

    public function insertClassic(Mark $mark) {
        $connection = $this->getConnection();

        $request = $connection->prepare('INSERT INTO Mark (x, y, teamId) VALUES (:x, :y, :teamId)');

        $request->execute(array(
            ':x' => $mark->x,
            ':y' => $mark->y,
            ':teamId' => $mark->teamId
        ));

        $mark->id = $connection->lastInsertId();

        return $mark;
    }

    public function updateMarkPlacement(Mark $mark) {
        $connection = $this->getConnection();

        $request = $connection->prepare('UPDATE Mark SET x=:x, y=:y WHERE id=:id');

        $request->bindParam(':x', $mark->x, PDO::PARAM_INT);
        $request->bindParam(':y', $mark->y, PDO::PARAM_INT);
        $request->bindParam(':id', $mark->id, PDO::PARAM_INT);

        $request->execute();

        return $mark;
    }
}