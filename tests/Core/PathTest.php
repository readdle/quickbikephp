<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\Core;

use PHPUnit\Framework\TestCase;
use Readdle\QuickBike\Core\Path;

class PathTest extends TestCase
{
    public function testJoin(): void
    {
        $this->assertEquals('/home/test/file.pdf', Path::join2('/home/test', 'file.pdf'));
        $this->assertEquals('/home/test/file.pdf', Path::join('/home/test', 'file.pdf'));

        $this->assertEquals('/home/test/tmp', Path::join('/home/test', 'tmp'));
        $this->assertEquals('/home/test/tmp/file.pdf', Path::join('/home/test', 'tmp', 'file.pdf'));

        $this->assertEquals('/home/test/tmp', Path::join('/home/test/', 'tmp'));
    }
}
