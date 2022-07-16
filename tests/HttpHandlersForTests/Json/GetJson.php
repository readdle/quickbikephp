<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\HttpHandlersForTests\Json;

use Readdle\QuickBike\Core\ActionInterface;
use Readdle\QuickBike\Core\Result\JsonResult;
use Readdle\QuickBike\Core\Result\ResultInterface;

class GetJson implements ActionInterface
{
    public function __invoke(): ResultInterface
    {
        return new JsonResult(['text' => 'Hello World']);
    }
}
