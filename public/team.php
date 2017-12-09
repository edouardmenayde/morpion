<?php declare(strict_types=1);

require __DIR__ . '/../src/Bootstrap.php';

use Epic\Controllers\Team;

$controller = new Team();

if ($_POST) {
    return $controller->create();
}

$controller->show();
