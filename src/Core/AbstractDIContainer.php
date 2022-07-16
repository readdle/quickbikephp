<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core;

use Psr\Container\ContainerInterface;
use Readdle\QuickBike\Core\Exception\DINotFoundException;

abstract class AbstractDIContainer implements ContainerInterface
{
    protected const DI_INTERFACE_MAP = [];
    protected const DI_TYPE_MAP = [];
    /**
     * @var array<string, object>
     */
    protected array $objectCache = [];

    public function get(string $id): object
    {
        if (array_key_exists($id, static::DI_INTERFACE_MAP)) {
            $id = static::DI_INTERFACE_MAP[$id];
        }

        if (!$this->has($id)) {
            throw new DINotFoundException('not found');
        }

        $method = static::DI_TYPE_MAP[$id];

        if (str_starts_with($method, 'notCached')) {
            return $this->$method();
        }

        if (array_key_exists($method, $this->objectCache)) {
            return $this->objectCache[$method];
        }

        $object = $this->$method();
        $this->objectCache[$method] = $object;
        return $object;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, static::DI_TYPE_MAP) ||
               array_key_exists($id, static::DI_INTERFACE_MAP);
    }
}
