<?php

namespace Epic\Repositories;

use Epic\Entities\Game;

class GameRepository extends Repository
{
    public function insert(Game $game)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('INSERT INTO Game (startedAt, initialPoints, doubleAttack, gridWidth, gridHeight, team1Id, team2Id) VALUES (:startedAt, :initialPoints, :doubleAttack, :gridWidth, :gridHeight, :team1Id, :team2Id)');

        $game->startedAt = date(DATE_ISO8601);

        $request->execute(array(
            ':startedAt' => $game->startedAt,
            ':initialPoints' => $game->initialPoints,
            ':doubleAttack' => $game->doubleAttack,
            ':gridWidth' => $game->gridWidth,
            ':gridHeight' => $game->gridHeight,
            ':team1Id' => $game->team1Id,
            ':team2Id' => $game->team2Id
        ));

        $game->id = $connection->lastInsertId();

        return $game;
    }
}
