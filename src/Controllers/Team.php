<?php declare(strict_types=1);

namespace Epic\Controllers;

use Epic\Entities\Game;
use Epic\Entities\Mark;
use Epic\Repositories\GameRepository;
use Epic\Repositories\MarkModelRepository;
use Epic\Repositories\MarkRepository;
use Epic\Repositories\TeamRepository;
use Epic\Templates\Template;

class Team
{
    private function validateTeam($team)
    {
        if (count($team['name']) < 0) {
            return false;
        }

        if (count($team['color']) < 0) {
            return false;
        }

        if (count($team['marks']) < 4 && count($team['marks']) > 8) {
            return false;
        }

        return true;
    }

    public function show()
    {
        $newTeams = $_POST;
        $teams = [];

        if (count($newTeams) !== 2) {
            die();
        }

        foreach ($newTeams as $newTeam) {
            if (!$this->validateTeam($newTeam)) {
                die();
            }

            $team = new Team();
            $team->name = $newTeam['name'];
            $team->color = $newTeam['color'];

            array_push($teams, $team);
        }

        $teamRepository = new TeamRepository();

        foreach ($teams as $key => $team) {
            $teams[$key] = $teamRepository->insert($team);
        }

        $gameRepository = new GameRepository();

        $game = new Game();

        $game->team1Id = $teams[0]->id;
        $game->team2Id = $teams[1]->id;
        $game->gridHeight = 3;
        $game->gridWidth = 3;
        $game->initialDoubleAttack = 30;
        $game->maxDoubleAttack = 30;
        $game->initialPoints = 10;

        $game = $gameRepository->insert($game);

        $markModelRepository = new MarkModelRepository();
        $markModels = $markModelRepository->getAll();

        $markRepository = new MarkRepository();

        $id = 0;
        foreach ($newTeams as $team) {
            foreach ($team['marks'] as $newMark) {
                $matchingMarkModel = null;

                foreach ($markModels as $markModel) {
                    if ($newMark == $markModel->id) {
                        $matchingMarkModel = $markModel;
                        break;
                    }
                }

                if ($matchingMarkModel) {
                    $mark = new Mark();
                    $mark->damage = $matchingMarkModel->damage;
                    $mark->hp = $matchingMarkModel->hp;
                    $mark->mana = $matchingMarkModel->mana;
                    $mark->markModelId = $newMark;
                    $mark->teamId = $teams[$id]->id;
                    $markRepository->insert($mark);
                }
            }

            $id += 1;
        }

//        $view = new Template();
//        $view->content = $homePageView->render('homepage.php');

//        echo $view->render('layout.php');
    }
}
