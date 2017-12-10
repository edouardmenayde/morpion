<?php

namespace Epic\Repositories;

use Epic\Entities\Action;
use PDO;

class ActionRepository extends Repository
{
    public function insert(Action $action)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('INSERT INTO Actions (type, x, y, markId, gameId) VALUE (:type, :x, :y, :markId, :gameId)');

        $request->bindParam(':type', $action->type, PDO::PARAM_STR);
        $request->bindParam(':x', $action->x, PDO::PARAM_INT);
        $request->bindParam(':y', $action->y, PDO::PARAM_INT);
        $request->bindParam(':markId', $action->markId, PDO::PARAM_INT);
        $request->bindParam(':gameId', $action->gameId, PDO::PARAM_INT);

        $request->execute();

        $action->id = $connection->lastInsertId();

        return $action;
    }

    public function count() {
        $connection = $this->getConnection();

        $request = $connection->query('SELECT COUNT(*) AS count FROM Actions');

        return $request->fetch()['count'];
    }
}
