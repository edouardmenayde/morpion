<?php

namespace Epic\Repositories;

use Epic\Entities\Game;

class GameRepository extends Repository
{
    public function insert(Game $game)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('INSERT INTO Game (initialPoints, initialDoubleAttack, maxDoubleAttack, gridWidth, gridHeight, team1Id, team2Id) VALUES (:initialPoints, :initialDoubleAttack, :maxDoubleAttack, :gridWidth, :gridHeight, :team1Id, :team2Id)');


        $request->execute(array(
            ':initialPoints' => $game->initialPoints,
            ':initialDoubleAttack' => $game->initialDoubleAttack,
            ':maxDoubleAttack' => $game->maxDoubleAttack,
            ':gridWidth' => $game->gridWidth,
            ':gridHeight' => $game->gridHeight,
            ':team1Id' => $game->team1Id,
            ':team2Id' => $game->team2Id
        ));

        $game->id = $connection->lastInsertId();

        return $game;
    }
}
