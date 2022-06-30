<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\Core;

use PHPUnit\Framework\TestCase;
use Readdle\QuickBike\Core\Config\CodeGenerator;

class ConfigCodeGeneratorTest extends TestCase
{
    public function testConfigBuilderFile(): void
    {
        $vars = require __DIR__ . '/CodeGenTestFiles/config.test.php';

        $code = new CodeGenerator('test');
        $code->addConstants($vars);

        $this->assertStringEqualsFile(__DIR__.'/CodeGenTestFiles/test1.php-code', $code->getGeneratedCode());
    }
}
