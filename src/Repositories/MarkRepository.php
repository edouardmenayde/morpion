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

    public function updateHP(Mark $mark) {
        $connection = $this->getConnection();

        $request = $connection->prepare('UPDATE Mark SET hp=:hp WHERE id=:id');

        $request->bindParam(':hp', $mark->hp, PDO::PARAM_INT);
        $request->bindParam(':id', $mark->id, PDO::PARAM_INT);

        $request->execute();

        return $mark;
    }

    public function updateDoubleAttack(Mark $mark) {
        $connection = $this->getConnection();

        $request = $connection->prepare('UPDATE Mark SET doubleAttack=:doubleAttack WHERE id=:id');

        $request->bindParam(':doubleAttack', $mark->doubleAttack, PDO::PARAM_INT);
        $request->bindParam(':id', $mark->id, PDO::PARAM_INT);

        $request->execute();

        return $mark;
    }

    public function updateMana(Mark $mark) {
        $connection = $this->getConnection();

        $request = $connection->prepare('UPDATE Mark SET mana=:mana WHERE id=:id');

        $request->bindParam(':mana', $mark->mana, PDO::PARAM_INT);
        $request->bindParam(':id', $mark->id, PDO::PARAM_INT);

        $request->execute();

        return $mark;
    }

    public function count() {
        $connection = $this->getConnection();

        $request = $connection->query('SELECT COUNT(*) AS count FROM Mark');

        return $request->fetch()['count'];
    }
}
