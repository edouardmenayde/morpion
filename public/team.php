<?php declare(strict_types=1);

require __DIR__ . '/../src/Bootstrap.php';

use Epic\Controllers\TeamController;

$controller = new TeamController();

if ($_POST) {
    return $controller->create();
}

$controller->show();
