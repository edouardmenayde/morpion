<?php declare(strict_types=1);

namespace Epic\Controllers;

use Epic\Entities\Game;
use Epic\Entities\Mark;
use Epic\Repositories\GameRepository;
use Epic\Repositories\MarkModelRepository;
use Epic\Repositories\MarkRepository;
use Epic\Repositories\TeamRepository;
use Epic\Templates\Template;
use Epic\Entities\MarkModelType;
use Whoops\Exception\ErrorException;

class Team
{
    private function validateTeam($team)
    {
        if (count_chars(trim($team['name'])) < 0) {
            return false;
        }

        if (count_chars(trim($team['color'])) < 0) {
            return false;
        }

        return true;
    }

    public function show($errors = null)
    {
        $markModelRepository = new MarkModelRepository();

        $markModels = $markModelRepository->getAll();

        $wizards = [];
        $warriors = [];
        $archers = [];

        foreach ($markModels as $markModel) {
            switch ($markModel->type) {
                case MarkModelType::warrior:
                    array_push($warriors, $markModel);
                    break;
                case MarkModelType::archer:
                    array_push($archers, $markModel);
                    break;
                case MarkModelType::wizard:
                    array_push($wizards, $markModel);
                    break;
                default:
                    throw new \Exception("Could not find matching type for this mark.");
            }
        }

        $teamView = new Template();
        $teamView->errors = $errors;
        $teamView->markModels = $markModels;
        $teamView->warriors = $warriors;
        $teamView->wizards = $wizards;
        $teamView->archers = $archers;
        $teamView->gameType = isset($_GET['type']) ? $_GET['type'] : 'classic';

        $view = new Template();
        try {

            $view->content = $teamView->render('team.php');

            echo $view->render('layout.php');
        } catch (\Exception $e) {
            echo $e;
        }
    }

    public function create()
    {
        $newTeams = [$_POST['team1'], $_POST['team2']];
        $teams = [];

        if (count($newTeams) !== 2) {
            return $this->show(['Deux équipes doivent être renseignés.']);
        }

        foreach ($newTeams as $newTeam) {
            if (!$this->validateTeam($newTeam)) {
                return $this->show(['Deux équipes doivent être renseignés.']);
            }

            $team = new Team();
            $team->name = trim($newTeam['name']);
            $team->color = trim($newTeam['color']);

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

        if ($_POST['type'] === 'advanced') {
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
                        $mark->doubleAttack = 20;
                        $mark->markModelId = $newMark;
                        $mark->teamId = $teams[$id]->id;
                        $markRepository->insertAdvanced($mark);
                    }
                }

                $id += 1;
            }
        }

        header('Location: ' . SITE_URL . 'game.php?id=' . $game->id);
        die();
    }
}
