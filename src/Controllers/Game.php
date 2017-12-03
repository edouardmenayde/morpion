<?php declare(strict_types=1);

namespace Epic\Controllers;

use Epic\Entities\Mark;
use Epic\Repositories\GameRepository;
use Epic\Repositories\MarkRepository;
use Epic\Templates\Template;


function isIllegalPlacement($game, $x, $y)
{
    function testForTeam($team, $x, $y)
    {
        $illegalPlacement = false;

        foreach ($team->marks as $mark) {
            if ($mark->x == $x && $mark->y == $y) {
                $illegalPlacement = true;
            }
        }

        return $illegalPlacement;
    }

    return testForTeam($game->team1, $x, $y) || testForTeam($game->team2, $x, $y);
}

class Game
{
    public function show()
    {
        $gameRepository = new GameRepository();

        $game = $gameRepository->get((int)$_GET['id']);

        $gameView = new Template();
        $gameView->game = $game;

        $view = new Template();
        $view->content = $gameView->render('game.php');

        echo $view->render('layout.php');
    }

    public function classic()
    {
        header('Content-type:application/json;charset=utf-8');

        $gameId = $_POST['gameId'];
        $x = $_POST['x'];
        $y = $_POST['y'];

        $gameRepository = new GameRepository();

        $game = $gameRepository->getWithMarks((int)$gameId);

        if (!$game) {
            http_response_code(500);

            echo json_encode(['error' => 'server_error']);
            return;
        }

        if ($x < 0 && $x >= $game->gridWidth && $y < 0 && $y >= $game->gridHeight) {
            http_response_code(400);

            echo json_encode(['error' => 'invalid_parameters']);
            return;
        }

        $teamId = null;

        if ($game->team1->marks[0]->id > $game->team2->marks[0]->id) { // check if most recent team who played is the first one
            $teamId = $game->team2->id;
        } else {
            $teamId = $game->team1->id;
        }

        if (isIllegalPlacement($game, $x, $y)) {
            http_response_code(400);

            echo json_encode(['error' => 'invalid_parameters']);
            return;
        }

        $markRepository = new MarkRepository();

        $newMark = new Mark();
        $newMark->x = $x;
        $newMark->y = $y;
        $newMark->teamId = $teamId;

        $newMark = $markRepository->insertClassic($newMark);

        echo json_encode($newMark);
    }
}
