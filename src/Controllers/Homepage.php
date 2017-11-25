<?php declare(strict_types=1);

namespace Epic\Controllers;

use Epic\Templates\Template;

class Homepage
{
    public function show()
    {
        $view = new Template();

        $view->content = $view->render('homepage.php');

        echo $view->render('layout.php');
    }
}
