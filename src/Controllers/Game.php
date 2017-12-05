<?php declare(strict_types=1);

namespace Epic\Controllers;

use Epic\Entities\Mark;
use Epic\Repositories\GameRepository;
use Epic\Repositories\MarkRepository;
use Epic\Services\ClassicGameService;
use Epic\Templates\Template;

function getNextTeam($game)
{
    if (count($game->team1->marks) == 0) {
        return $game->team1->id;
    }

    if (count($game->team2->marks) == 0) {
        return $game->team2->id;
    }

    if ($game->team1->marks[0]->id > $game->team2->marks[0]->id) { // check if most recent team who played is the first one
        return $game->team2->id;
    }

    return $game->team1->id;
}

class Game
{
    public function show()
    {
        $gameRepository = new GameRepository();

        $game = $gameRepository->getWithMarks((int)$_GET['id']);

        $gameView = new Template();
        $gameView->game = $game;

        $view = new Template();
        $view->content = $gameView->render('game.php');

        echo $view->render('layout.php');
    }

    public function validateClassic($post)
    {
        if (!isset($post['gameId']) && $post['gameId'] < 0) {
            return false;
        }

        if (!isset($post['x']) && $post['x'] < 0) {
            return false;
        }

        if (!isset($post['y']) && $post['y'] < 0) {
            return false;
        }

        return true;
    }

    public function classic()
    {
        header('Content-type:application/json;charset=utf-8');

        if (!$_POST || !$this->validateClassic($_POST)) {
            http_response_code(500);

            echo json_encode(['error' => 'server_error']);
            return;
        }

        $gameId = $_POST['gameId'];
        $x = $_POST['x'];
        $y = $_POST['y'];

        $gameRepository = new GameRepository();

        $game = $gameRepository->getWithMarks((int)$gameId);

        if (!$game) {
            http_response_code(400);

            echo json_encode(['error' => 'invalid_parameters', 'context' => 'gameId provided is not valid']);
            return;
        }

        if ($x < 0 && $x >= $game->gridWidth && $y < 0 && $y >= $game->gridHeight) {
            http_response_code(400);

            echo json_encode(['error' => 'invalid_parameters', 'context' => 'x, y coordinates not valid']);
            return;
        }

        $classicGameService = new ClassicGameService($game);

        $teamId = getNextTeam($game);

        if ($classicGameService->isIllegalPlacement($x, $y)) {
            http_response_code(400);

            echo json_encode(['error' => 'invalid_parameters', 'context' => 'x, y coordinates illegals']);
            return;
        }

        if ($classicGameService->isGameEnded()) {
            http_response_code(400);

            echo json_encode(['error' => 'invalid_action', 'context' => 'game is ended']);
            return;
        }

        $markRepository = new MarkRepository();

        $newMark = new Mark();
        $newMark->x = $x;
        $newMark->y = $y;
        $newMark->teamId = $teamId;

        $newMark = $markRepository->insertClassic($newMark);

        $classicGameService->registerMark($newMark);

        $winner = $classicGameService->getWinner();

        if ($winner) {
            $game->winnerId = (int) $winner;
            $gameRepository->updateWinner($game);
        }

        $response = [
            'isEnded' => $classicGameService->isGameEnded(),
            'game' => $game,
            'newMark' => $newMark
        ];

        http_response_code(200);
        echo json_encode($response);
    }
}
