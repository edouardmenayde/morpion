<?php declare(strict_types=1);

namespace Epic\Controllers;

use Epic\Entities\MarkModelType;
use Epic\Repositories\MarkModelRepository;
use Epic\Templates\Template;

class Homepage
{
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
