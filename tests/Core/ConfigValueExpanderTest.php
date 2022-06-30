<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\Core;

use PHPUnit\Framework\TestCase;
use Readdle\QuickBike\Core\Config\ValueExpander;

class ConfigValueExpanderTest extends TestCase
{
    public function testExpandEnv(): void
    {
        putenv('ENV1=test');
        putenv('OTHER_ENV=12345');

        $expanded = ValueExpander::expandValue('%env(ENV1)%-%env(OTHER_ENV)%');

        $this->assertEquals('test-12345', $expanded);

        putenv('ENV1');
        putenv('OTHER_ENV');
    }
}
