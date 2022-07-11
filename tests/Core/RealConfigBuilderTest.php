<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\Core;

use PHPUnit\Framework\TestCase;
use Readdle\QuickBike\Core\Config\CacheChecker;
use Readdle\QuickBike\Core\Config\RealConfigBuilder;
use Readdle\QuickBike\Core\Exception\ConfigBuilderException;

class RealConfigBuilderTest extends TestCase
{
    /**
     * @throws ConfigBuilderException
     */
    public function testBasic(): void
    {
        $resultRuntimeConfigPath = __DIR__.'/ConfigBuilderTestFiles/result.php-code';
        $envFilePath = __DIR__.'/ConfigBuilderTestFiles/three-nv-file';
        $configDir = __DIR__.'/ConfigBuilderTestFiles/';

        $builder = new RealConfigBuilder(
            envFilePath: $envFilePath,
            configDir: $configDir,
            runtimeConfigPath: $resultRuntimeConfigPath
        );
        $builder->build();

        $result = file_get_contents($resultRuntimeConfigPath);
        // remove cache line for comparison
        $result = preg_replace('|sig:.+\n|m', "test\n", $result);


        $checker = new CacheChecker($resultRuntimeConfigPath, $envFilePath, $configDir);
        $this->assertTrue($checker->checkIfConfigActual());


        $this->assertStringEqualsFile(__DIR__.'/CodeGenTestFiles/test1.php-code', $result);
    }
}
