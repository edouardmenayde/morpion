<?php declare(strict_types=1);

/**
 * Edouard MENAYDE p1607161
 * Aristide DULLIN p1501531
 */

require __DIR__ . '/../src/Bootstrap.php';

use Epic\Controllers\HomepageController;

$controller = new HomepageController();

$controller->show();
