<?php declare(strict_types=1);

require __DIR__ . '/../src/Bootstrap.php';

use Epic\Controllers\Homepage;

$controller = new Homepage();

$controller->show();
