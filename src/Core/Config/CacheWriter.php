<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Config;

class CacheWriter
{
    /**
     * @var array<string, string>
     */
    protected array $cache = [];

    public function addFile(string $fullPath): void
    {
        $name = basename($fullPath);
        $this->cache[$name] = Utils::cacheSignature($fullPath);
    }

    public function containsFile(string $fullPath): bool
    {
        $name = basename($fullPath);
        return array_key_exists($name, $this->cache);
    }

    public function getJSONCacheLine(): string
    {
        return json_encode($this->cache);
    }
}
