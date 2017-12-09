<?php declare(strict_types=1);

require __DIR__ . '/../src/Bootstrap.php';

use Epic\Controllers\Game;

$controller = new Game();

$controller->advanced();
