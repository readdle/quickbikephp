<?php

namespace Readdle\QuickBike\Core\Config;

use Readdle\QuickBike\Core\Exception\ConfigBuilderException;
use Readdle\QuickBike\Core\Path;

/** @internal  */
class Utils
{
    public static function cacheSignature(string $filePath): string
    {
        $s = @stat($filePath);
        if ($s === false) {
            return '-';
        }
        return $s['size'].'-'.$s['mtime'];
    }


    /**
     * @param array<string, mixed> $ar
     * @param callable $mapFn
     * @return array<string, mixed>
     */
    public static function arrayMapStringLeaves(array $ar, callable $mapFn): array
    {
        $result = [];
        foreach ($ar as $a => $r) {
            if (is_string($r)) {
                $result[$a] = $mapFn($r);
            } elseif (is_array($r)) {
                $result[$a] = self::arrayMapStringLeaves($r, $mapFn);
            } else {
                $result[$a] = $r;
            }
        }
        return $result;
    }
}
