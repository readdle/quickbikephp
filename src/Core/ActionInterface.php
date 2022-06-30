<?php

namespace Readdle\QuickBike\Core;

use Readdle\QuickBike\Core\Result\ResultInterface;
use Readdle\QuickBike\Core\Result\TemplateResult;

interface ActionInterface
{
    public function __invoke(): ResultInterface;
}
