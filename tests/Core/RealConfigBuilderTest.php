<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\Core;

use PHPUnit\Framework\TestCase;
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

        $builder = new RealConfigBuilder(
            envFilePath: __DIR__.'/ConfigBuilderTestFiles/three-nv-file',
            configDir: __DIR__.'/ConfigBuilderTestFiles/',
            runtimeConfigPath: $resultRuntimeConfigPath
        );
        $builder->build();

        $result = file_get_contents($resultRuntimeConfigPath);
        // remove cache line for comparison
        $result = preg_replace('|sig:.+\n|m', "test\n", $result);

        $this->assertStringEqualsFile(__DIR__.'/CodeGenTestFiles/test1.php-code', $result);
    }
}
