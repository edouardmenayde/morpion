<?php

namespace Epic\Controllers;

use Epic\Entities\Game;
use Epic\Entities\GameType;
use Epic\Entities\Mark;
use Epic\Entities\Team;
use Epic\Repositories\GameRepository;
use Epic\Repositories\MarkModelRepository;
use Epic\Repositories\MarkRepository;
use Epic\Repositories\TeamRepository;
use Epic\Templates\Template;
use Epic\Entities\MarkModelType;

class Validator
{
    private $errors = [];

    public function isInferiorOrEqualTo(int $length, $field, $value)
    {
        if (strlen(trim($value)) > $length) {
            $this->errors[strtolower($value)] = 'La valeur pour le champ <i>' . $field . '</i> doit être inférieur ou eǵal à ' . $length . ' caractères';
        }
    }

    public function isSuperiorThan(int $length, $field, $value)
    {
        if (strlen(trim($value)) <= $length) {
            $this->errors[strtolower($value)] = 'La valeur pour le champ <i>' . $field . '</i> doit être supérieur à ' . $length . ' caractères';
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function validate()
    {
        return count($this->errors) == 0;
    }
}

class TeamController
{
    public function show($errors = null)
    {
        try {
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

            $view->content = $teamView->render('team.php');

            echo $view->render('layout.php');
        } catch (\Exception $e) {
            echo $e;
        }
    }

    public function create()
    {
        try {
            $newTeams = [$_POST['team1'], $_POST['team2']];
            $gridsize = isset($_GET['gridsize']) ? $_GET['gridsize'] : 3;
            if ($gridsize != 3 && $gridsize != 4) {
                $gridsize = 3;
            }
            $doubleAttack = isset($_POST['doubleAttack']) ? $_POST['doubleAttack'] : 10;
            if ($doubleAttack < 0) {
                $doubleAttack = 10;
            }

            $teams = [];

            $validator = new Validator();

            if (count($newTeams) !== 2) {
                $this->show(['Deux équipes doivent être renseignés.)']);
                return;
            }

            foreach ($newTeams as $newTeam) {
                $validator->isInferiorOrEqualTo(255, "Nom d'équipe", $newTeam['name']);
                $validator->isSuperiorThan(0, "Nom d'équipe", $newTeam['name']);

                $validator->isInferiorOrEqualTo(255, "Couleur d'équipe", $newTeam['color']);
                $validator->isSuperiorThan(0, "Couleur d'équipe", $newTeam['color']);

                $team = new Team();
                $team->name = trim($newTeam['name']);
                $team->color = trim($newTeam['color']);

                array_push($teams, $team);
            }

            if (!$validator->validate()) {
                $this->show($validator->getErrors());
                return;
            }

            $teamRepository = new TeamRepository();

            foreach ($teams as $key => $team) {
                $teams[$key] = $teamRepository->insert($team);
            }

            $gameRepository = new GameRepository();

            $game = new Game();

            $game->team1Id = $teams[0]->id;
            $game->team2Id = $teams[1]->id;
            $game->gridHeight = $gridsize;
            $game->gridWidth = $gridsize;
            $game->initialDoubleAttack = $doubleAttack;
            $game->maxDoubleAttack = 30;
            $game->initialPoints = 10;

            switch ($_POST['type']) {
                case GameType::classic:
                    $game->type = GameType::classic;
                    break;
                case GameType::advanced:
                    $game->type = GameType::advanced;
                    break;
                default:
                    throw new \Exception('Le type n\'est pas reconnu');
            }

            $game = $gameRepository->insert($game);

            if ($game->type === GameType::advanced) {
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
        catch (\Exception $e) {
            echo $e;
        }
    }
}
