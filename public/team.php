<?php declare(strict_types=1);

require __DIR__ . '/../src/Bootstrap.php';

if (!$_POST) {
    die();
}

use Epic\Controllers\Team;

$controller = new Team();

$controller->show();
