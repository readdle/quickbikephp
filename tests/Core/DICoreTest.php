<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\Core;

use AutoRoute\AutoRoute;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Readdle\Database\FQDB;
use Readdle\QuickBike\Core\DICore;
use Readdle\QuickBike\Test\DITestClasses\DITestClass;
use Symfony\Component\HttpFoundation\Session\Session;

class DICoreTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        putenv('QUICKBIKE_NO_CONFIG_UPDATE=1');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        putenv('QUICKBIKE_NO_CONFIG_UPDATE');
    }

    public function testDefaultServices(): void
    {
        $di = new DICore();
        $logger = $di->get(LoggerInterface::class);
        $this->assertInstanceOf(LoggerInterface::class, $logger);

        $autoRoute = $di->get(AutoRoute::class);
        $this->assertInstanceOf(AutoRoute::class, $autoRoute);

        /** @var FQDB $fqdb */
        $fqdb = $di->get(FQDB::class);
        $this->assertEquals('4', $fqdb->queryValue('SELECT 2+2'));
    }

    public function testConstructorArgs(): void
    {
        $di = new DICore();
        /** @var DITestClass $object */
        $object = $di->get(DITestClass::class);
        $this->assertEquals(42, $object->otherValue);
        $this->assertEquals('abc', $object->someToken);
    }
}
