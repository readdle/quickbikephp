<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\DITestClasses;

use Readdle\Database\FQDB;

class DITestClass
{
    public function __construct(
        public readonly FQDB $fqdb,
        public readonly string $someToken,
        public readonly int $otherValue
    ) {
    }
}
