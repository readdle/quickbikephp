<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\Core;

use PHPUnit\Framework\TestCase;
use Readdle\QuickBike\Core\DICore;
use Readdle\QuickBike\Core\Engine;
use Symfony\Component\HttpFoundation\Request;

class EngineTest extends TestCase
{
    public function testBasicGet(): void
    {
        putenv('QUICKBIKE_NO_CONFIG_UPDATE=1');

        $di = new DICore();
        $engine = new Engine($di);
        $request = Request::create('/');

        $response = $engine->generateResponseForRequest($request);

        $this->assertEquals('Hello World', $response->getContent());
        putenv('QUICKBIKE_NO_CONFIG_UPDATE');
    }
}
