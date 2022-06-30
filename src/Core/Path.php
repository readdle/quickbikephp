<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core;

/**
 * Path Helper Class — contains static methods that return paths in special pre-configured folders
 *
 * @package Readdle\QuickBike\Core
 */
class Path
{
    public static function join(string ...$parts): string
    {
        return array_reduce($parts, [Path::class, 'join2'], '');
    }

    public static function join2(string $a, string $b): string
    {
        return rtrim($a, DIRECTORY_SEPARATOR).'/'.ltrim($b, DIRECTORY_SEPARATOR);
    }


    /**
     * Returns paths in application root
     * (not recommended if there is a specific function below)
     * @param string ...$parts
     * @return string
     */
    public static function root(string ...$parts): string
    {
        return self::join(APP_ROOT, ...$parts);
    }

    /**
     * Returns paths in application public folder
     * @param string ...$parts
     * @return string
     */
    public static function public(string ...$parts): string
    {
        return self::join(APP_ROOT, 'public', ...$parts);
    }


    /**
     * Returns paths in applications views folder
     * @param string ...$parts
     * @return string
     */
    public static function views(string ...$parts): string
    {
        return self::join(APP_ROOT, 'views', ...$parts);
    }

    /**
     * Returns paths in application config folder
     * @param string ...$parts
     * @return string
     */
    public static function cache(string ... $parts): string
    {
        return self::join(APP_ROOT, 'cache', ...$parts);
    }
}
