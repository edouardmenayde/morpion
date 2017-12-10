<?php

namespace Epic\Controllers;

use Epic\Entities\GameType;
use Epic\Entities\MarkModelType;
use Epic\Repositories\MarkModelRepository;
use Epic\Templates\Template;

class HomepageController
{
    public function play()
    {
        $type = isset($_POST['type']) ? $_POST['type'] : GameType::classic;
        $gridsize = isset($_POST['gridsize']) ? $_POST['gridsize'] : 3;
        $doubleAttack = isset($_POST['doubleAttack']) ? $_POST['doubleAttack'] : 10;

        if ($type != GameType::classic && $type != GameType::advanced) {
            $type = GameType::classic;
        }

        if ($gridsize != 3 && $gridsize != 4) {
            $gridsize = 3;
        }

        if ($doubleAttack < 0) {
            $doubleAttack = 20;
        }

        $url = 'Location: ' . SITE_URL . 'team.php?type=' . $type . '&gridsize=' . $gridsize;

        if ($type == GameType::advanced) {
            $url .= '&doubleAttack=' . $doubleAttack;
        }

        header($url);
        die();
    }

    public function show()
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

            $homePageView = new Template();
            $homePageView->markModels = $markModels;
            $homePageView->warriors = $warriors;
            $homePageView->wizards = $wizards;
            $homePageView->archers = $archers;

            $view = new Template();
            $view->content = $homePageView->render('homepage.php');

            echo $view->render('layout.php');
        } catch (\Exception $e) {
            echo $e;
        }
    }
}
