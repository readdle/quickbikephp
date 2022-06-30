<?php
declare(strict_types=1);

use Readdle\QuickBike\Core\DICore;

define('APP_ROOT', __DIR__);
require './vendor/autoload.php';

if (!defined('NO_DI_CORE')) {
    return new DICore();
}
