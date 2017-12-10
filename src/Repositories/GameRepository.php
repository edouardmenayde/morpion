<?php

namespace Epic\Repositories;

use Epic\Entities\Action;
use Epic\Entities\Game;
use Epic\Entities\Mark;
use Epic\Entities\MarkModel;
use Epic\Entities\Team;
use PDO;

class GameRepository extends Repository
{
    public function insert(Game $game)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('INSERT INTO Game (initialPoints, initialDoubleAttack, maxDoubleAttack, gridWidth, gridHeight, type,  team1Id, team2Id) VALUES (:initialPoints, :initialDoubleAttack, :maxDoubleAttack, :gridWidth, :gridHeight, :type, :team1Id, :team2Id)');

        $request->execute(array(
            ':initialPoints' => $game->initialPoints,
            ':initialDoubleAttack' => $game->initialDoubleAttack,
            ':maxDoubleAttack' => $game->maxDoubleAttack,
            ':gridWidth' => $game->gridWidth,
            ':gridHeight' => $game->gridHeight,
            ':type' => $game->type,
            ':team1Id' => $game->team1Id,
            ':team2Id' => $game->team2Id
        ));

        $game->id = $connection->lastInsertId();

        return $game;
    }

    public function updateWinner(Game $game)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('UPDATE Game SET winnerId=:winnerId, ended=TRUE WHERE id=:id');

        $request->bindParam(':winnerId', $game->winnerId, PDO::PARAM_INT);
        $request->bindParam(':id', $game->id, PDO::PARAM_INT);

        $request->execute();

        return $game;
    }

    public function updateStatus(Game $game)
    {
        $connection = $this->getConnection();

        $request = $connection->prepare('UPDATE Game SET ended=:ended WHERE id=:id');

        $request->bindParam(':ended', $game->ended, PDO::PARAM_INT);
        $request->bindParam(':id', $game->id, PDO::PARAM_INT);

        $request->execute();

        return $game;
    }

    public function getWithMarks(int $id)
    {

        $connection = $this->getConnection();

        $request = $connection->prepare('
          SELECT 
          g.id, g.gridWidth, g.gridHeight, g.winnerId, g.ended, g.type, g.maxDoubleAttack,
          t1.id AS t1Id, t1.name AS t1Name, t1.color AS t1Color, 
          m1.id AS m1Id, m1.x AS m1X , m1.y AS m1Y, m1.hp AS m1Hp, m1.damage AS m1Damage, m1.mana AS m1Mana, m1.doubleAttack AS m1DoubleAttack,
          mm1.name AS mm1Name, mm1.type AS mm1Type, mm1.icon AS mm1Icon, mm1.hp AS mm1Hp,
          t2.id AS t2Id, t2.name AS t2Name, t2.color AS t2Color, 
          m2.id AS m2Id, m2.x AS m2X , m2.y AS m2Y, m2.hp AS m2Hp, m2.damage AS m2Damage, m2.mana AS m2Mana, m2.doubleAttack AS m2DoubleAttack,
          mm2.name AS mm2Name, mm2.type AS mm2Type, mm2.icon AS mm2Icon, mm2.hp AS mm2Hp,
          a.id AS aId, a.type AS aType, a.x AS aX, a.y AS aY, a.gameId AS aGameId, a.markId AS aMarkId
          FROM Game g
          INNER JOIN Team t1 ON g.team1Id = t1.id
          LEFT JOIN Mark m1 ON t1.id = m1.teamId
          LEFT JOIN MarkModel mm1 ON mm1.id = m1.markModelId
          INNER JOIN Team t2 ON g.team2Id = t2.id
          LEFT JOIN Mark m2 ON t2.id = m2.teamId
          LEFT JOIN MarkModel mm2 ON mm2.id = m2.markModelId
          LEFT JOIN Actions a ON a.gameId = g.id
          WHERE g.id = :id
          ORDER BY m1.id DESC, m2.id DESC, a.id DESC
          ');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        $results = $request->fetchAll(PDO::FETCH_ASSOC);

//        echo '<pre>';
//        var_dump($results);
//        echo '</pre>';

        if (count($results) == 0) {
            return false;
        }

        $game = new Game();
        $game->id = $results[0]['id'];
        $game->winnerId = $results[0]['winnerId'];
        $game->ended = $results[0]['ended'];
        $game->type = $results[0]['type'];
        $game->gridHeight = $results[0]['gridHeight'];
        $game->gridWidth = $results[0]['gridWidth'];
        $game->maxDoubleAttack = $results[0]['maxDoubleAttack'];
        $game->actions = [];

        $team1 = new Team();
        $team1->id = $results[0]['t1Id'];
        $team1->name = $results[0]['t1Name'];
        $team1->color = $results[0]['t1Color'];

        $game->team1 = clone $team1;

        $team2 = new Team();
        $team2->id = $results[0]['t2Id'];
        $team2->name = $results[0]['t2Name'];
        $team2->color = $results[0]['t2Color'];

        $game->team2 = clone $team2;

        $game->teams = [$game->team1, $game->team2];

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
                    $mark->damage = $item['m1Damage'];
                    $mark->mana = $item['m1Mana'];
                    $mark->hp = $item['m1Hp'];
                    $mark->doubleAttack = $item['m1DoubleAttack'];
                    $mark->teamId = $game->team1->id;
                    $mark->team = $team1;

                    $markModel = new MarkModel();
                    $markModel->name = $item['mm1Name'];
                    $markModel->type = $item['mm1Type'];
                    $markModel->icon = $item['mm1Icon'];
                    $markModel->hp = $item['mm1Hp'];
                    $mark->markModel = $markModel;

                    array_push($game->team1->marks, $mark);
                }
            }

            $mark2Id = $item['m2Id'];

            if ($mark2Id) {
                $matchForMark2 = false;

                foreach ($game->team2->marks as $mark) {
                    if ($mark->id === $mark2Id) {
                        $matchForMark2 = true;
                    }
                }

                if (!$matchForMark2) {
                    $mark = new Mark();
                    $mark->id = $mark2Id;
                    $mark->x = $item['m2X'];
                    $mark->y = $item['m2Y'];
                    $mark->damage = $item['m2Damage'];
                    $mark->mana = $item['m2Mana'];
                    $mark->hp = $item['m2Hp'];
                    $mark->doubleAttack = $item['m2DoubleAttack'];
                    $mark->teamId = $game->team2->id;
                    $mark->team = $team2;

                    $markModel = new MarkModel();
                    $markModel->name = $item['mm2Name'];
                    $markModel->type = $item['mm2Type'];
                    $markModel->icon = $item['mm2Icon'];
                    $markModel->hp = $item['mm2Hp'];
                    $mark->markModel = $markModel;

                    array_push($game->team2->marks, $mark);
                }
            }

            $actionId = $item['aId'];

            if ($actionId) {
                $matchForAction = null;

                foreach ($game->actions as $action) {
                    if ($action->id == $actionId) {
                        $matchForAction = true;
                    }
                }

                if (!$matchForAction) {
                    $action = new Action();
                    $action->id = $actionId;
                    $action->x = $item['aX'];
                    $action->y = $item['aY'];
                    $action->type = $item['aType'];
                    $action->gameId = $item['aGameId'];
                    $action->markId = $item['aMarkId'];

                    array_push($game->actions, $action);
                }
            }
        }

        return $game;
    }

    public function count()
    {
        $connection = $this->getConnection();

        $request = $connection->query('SELECT COUNT(*) AS count FROM Game');

        return $request->fetch()['count'];
    }
}
