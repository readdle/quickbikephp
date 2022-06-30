<?php
declare(strict_types=1);

if ($argc > 1 && $argv[1] == 'dummy') {
    file_put_contents(
        __DIR__.'/generated/GeneratedRuntimeConfig.php',
        '<?'.'php
//This file was generated during container build.It must be rewritten on container start. 
namespace Readdle\\QuickBike\\Config;

class GeneratedRuntimeConfig
{
}
'
    );

    echo "OK\n";
    return;
}

use Readdle\QuickBike\Core\ConfigBuilder;
use Readdle\QuickBike\Core\Exception\ConfigBuilderException;

const NO_DI_CORE = true;
require dirname(__DIR__).'/bootstrap.php';

try {
    (new ConfigBuilder())->generate();
} catch (ConfigBuilderException $e) {
    echo 'failed to generate config: '. $e->getMessage();
}

echo "OK\n";
