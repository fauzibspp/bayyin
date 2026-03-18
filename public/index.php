<?php

declare(strict_types=1);

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
// require_once dirname(__DIR__) . '/app/Core/Bootstrap.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\Bootstrap;

Bootstrap::init();

$app = new App();
$app->run();