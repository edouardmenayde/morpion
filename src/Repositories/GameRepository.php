<?php

namespace Epic\Repositories;

use Epic\Entities\Game;
use Epic\Entities\Mark;
use Epic\Entities\Team;
use PDO;

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

    public function updateWinner(Game $game)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('UPDATE Game SET winnerId=:winnerId WHERE id=:id');

        $request->bindParam(':winnerId', $game->winnerId, PDO::PARAM_INT);
        $request->bindParam(':id', $game->id, PDO::PARAM_INT);

        $request->execute();

        return $game;
    }

    public function getWithMarks(int $id)
    {

        $connection = $this->getConnection();

        $request = $connection->prepare('
          SELECT 
          g.id, g.gridWidth, g.gridHeight, g.winnerId,
          t1.id AS t1Id, t1.name AS t1Name, t1.color AS t1Color, 
          m1.id AS m1Id, m1.x AS m1X , m1.y AS m1Y,
          t2.id AS t2Id, t2.name AS t2Name, t2.color AS t2Color, 
          m2.id AS m2Id, m2.x AS m2X, m2.y AS m2Y
          FROM Game g
          INNER JOIN Team t1 ON g.team1Id = t1.id
          LEFT JOIN Mark m1 ON t1.id = m1.teamId
          INNER JOIN Team t2 ON g.team2Id = t2.id
          LEFT JOIN Mark m2 ON t2.id = m2.teamId
          WHERE g.id = :id
          ORDER BY m1.id DESC, m2.id DESC
          ');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        $results = $request->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) == 0) {
            return false;
        }


        $game = new Game();
        $game->id = $results[0]['id'];
        $game->winnerId = $results[0]['winnerId'];
        $game->gridHeight = $results[0]['gridHeight'];
        $game->gridWidth = $results[0]['gridWidth'];

        $game->team1 = new Team();
        $game->team1->id = $results[0]['t1Id'];
        $game->team1->name = $results[0]['t1Name'];
        $game->team1->color = $results[0]['t1Color'];

        $game->team2 = new Team();
        $game->team2->id = $results[0]['t2Id'];
        $game->team2->name = $results[0]['t2Name'];
        $game->team2->color = $results[0]['t2Color'];

        foreach ($results as $item) {
            $mark1Id = $item['m1Id'];

            if ($mark1Id) {
                $matchForMark1 = false;

                foreach ($game->team1->marks as $mark) {
                    if ($mark->id === $mark1Id) {
                        $matchForMark1 = true;
                    }
                }

                if (!$matchForMark1) {
                    $mark = new Mark();
                    $mark->id = $mark1Id;
                    $mark->x = $item['m1X'];
                    $mark->y = $item['m1Y'];
                    $mark->teamId = $game->team1->id;
                    array_push($game->team1->marks, $mark);
                }
            }

            $mark2Id = $item['m2Id'];

            if ($mark2Id) {
                $matchForMark2 = false;

                foreach ($game->team1->marks as $mark) {
                    if ($mark->id === $mark2Id) {
                        $matchForMark2 = true;
                    }
                }

                if (!$matchForMark2) {
                    $mark = new Mark();
                    $mark->id = $mark2Id;
                    $mark->x = $item['m2X'];
                    $mark->y = $item['m2Y'];
                    $mark->teamId = $game->team2->id;
                    array_push($game->team2->marks, $mark);
                }
            }
        }

        return $game;
    }
}
