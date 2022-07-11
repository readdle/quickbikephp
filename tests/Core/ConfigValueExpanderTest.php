<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\Core;

use PHPUnit\Framework\TestCase;
use Readdle\QuickBike\Core\Config\ValueExpander;
use Readdle\QuickBike\Core\Path;
use ReflectionClass;
use ReflectionException;

class ConfigValueExpanderTest extends TestCase
{
    public function testExpandEnv(): void
    {
        putenv('ENV1=test');
        putenv('OTHER_ENV=12345');

        $this->assertEquals('test', ValueExpander::expandValue('%env(ENV1)%'));
        $this->assertEquals('12345', ValueExpander::expandValue('%env(OTHER_ENV)%'));

        $expanded = ValueExpander::expandValue('%env(ENV1)%-%env(OTHER_ENV)%');
        $this->assertEquals('test-12345', $expanded);

        putenv('ENV1');
        putenv('OTHER_ENV');
    }

    public function testExpandFile(): void
    {
        $expanded = ValueExpander::expandValue('%file(../LICENSE)%');
        $this->assertStringEqualsFile(Path::root('LICENSE'), $expanded);

        $expanded = ValueExpander::expandValue('%file(not_exists)%');
        $this->assertEquals('expansion error: file not exists', $expanded);

        $exArray = ValueExpander::expandValue('%json_file(../composer.json)%');
        $this->assertArrayHasKey('description', $exArray);
        $this->assertArrayHasKey('autoload', $exArray);
    }


    /**
     * @throws ReflectionException
     */
    public function testFileExpandFunctions(): void
    {
        $class = new ReflectionClass(ValueExpander::class);
        $method = $class->getMethod('resolvePathRelativeToConfig');

        $this->assertEquals('/test.file', $method->invoke(null, '/test.file'));

        $expected = Path::root('config', 'test.file');
        $this->assertEquals($expected, $method->invoke(null, 'test.file'));

        $expected = Path::root('config', 'test.file');
        $this->assertEquals($expected, $method->invoke(null, './test.file'));

        $expected = Path::root('LICENSE');
        $this->assertEquals($expected, $method->invoke(null, '../LICENSE'));
    }
}
