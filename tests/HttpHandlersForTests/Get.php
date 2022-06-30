<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\HttpHandlersForTests;

use Readdle\QuickBike\Core\ActionInterface;
use Readdle\QuickBike\Core\Result\DataResult;
use Readdle\QuickBike\Core\Result\ResultInterface;

class Get implements ActionInterface
{
    public function __invoke(): ResultInterface
    {
        return new DataResult('Hello World');
    }
}
