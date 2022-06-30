<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core;

use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Readdle\QuickBike\Config\CodeConfig;
use Readdle\QuickBike\Config\GeneratedRuntimeConfig;
use Readdle\QuickBike\Core\Exception\ConfigBuilderException;
use Readdle\QuickBike\Core\Exception\DIException;
use ReflectionClass;
use ReflectionException;
use ReflectionUnionType;

/** @internal  */
class DICore
{

    /**
     * @var ContainerInterface[]
     */
    protected array $appContainers;

    public function __construct()
    {
        $gen = new ConfigBuilder();
        try {
            $gen->buildIfNeeded();
        } catch (ConfigBuilderException $e) {
            echo 'failed to generate config: '. $e->getMessage();
        }
        $this->prepareAppContainers();
    }

    private function prepareAppContainers(): void
    {
        $containers = array_merge([DefaultServices::class], CodeConfig::DI_APP_CONTAINERS);
        $this->appContainers = array_map(function ($className) {
            return new $className();
        }, $containers);
    }


    /**
     * @throws NotFoundExceptionInterface|ContainerExceptionInterface
     */
    public function get(string $className): object
    {
        foreach ($this->appContainers as $appContainer) {
            if ($appContainer->has($className)) {
                return $appContainer->get($className);
            }
        }

        try {
            $class = new ReflectionClass($className);
        } catch (ReflectionException $e) {
            throw new DIException('ReflectionException: '.$e->getMessage());
        }

        $constructor = $class->getConstructor();
        if ($constructor === null) {
            return new $className();
        }
        $params = $constructor->getParameters();
        $arguments = [];


        $constructorArgs = null;

        if (defined(GeneratedRuntimeConfig::class.'::DI_CONSTRUCTOR_ARGUMENTS')) {
            if (array_key_exists($className, GeneratedRuntimeConfig::DI_CONSTRUCTOR_ARGUMENTS)) {
                $constructorArgs = GeneratedRuntimeConfig::DI_CONSTRUCTOR_ARGUMENTS[$className];
            }
        }

        foreach ($params as $param) {
            if ($constructorArgs !== null) {
                if (array_key_exists($param->getName(), $constructorArgs)) {
                    $arguments[] = $constructorArgs[$param->getName()];
                    continue;
                }
            }

            if (!$param->hasType()) {
                throw new DIException('unable to wire parameter without type');
            }

            $t = $param->getType();

            if ($t instanceof \ReflectionNamedType) {
                if (!in_array($t->getName(), ['string', 'array', 'bool', 'int'])) {
                    $arguments[] = $this->get($t->getName());
                    continue;
                }
            }
            throw new DIException('unable to wire argument '. $param->getName(). ' of class '.$className);
        }

        return new $className(...$arguments);
    }
}
