<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class DIException extends Exception implements ContainerExceptionInterface
{
}
