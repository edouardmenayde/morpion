<?php

namespace Epic\Repositories;

use Epic\Entities\Mark;

class MarkRepository extends Repository
{
    public function insert(Mark $mark)
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
}