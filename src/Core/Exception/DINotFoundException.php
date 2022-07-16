<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Exception;

use Psr\Container\NotFoundExceptionInterface;

class DINotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
