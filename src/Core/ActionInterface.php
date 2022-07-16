<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core;

use Readdle\QuickBike\Core\Result\ResultInterface;

interface ActionInterface
{
    public function __invoke(): ResultInterface;
}
