<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Config;

use Readdle\QuickBike\Core\Path;

/** @internal  */
class CacheChecker
{
    public function __construct(
        protected readonly string $generatedPath,
        protected readonly string $threeNvFullPath,
        protected readonly string $configDir
    ) {
    }


    public function checkIfConfigActual(): bool
    {
        if (!file_exists($this->generatedPath)) {
            return false;
        }

        $fileHeader = file_get_contents(
            $this->generatedPath,
            false,
            null,
            0,
            4096
        );

        if (preg_match('|//sig:(.+?)\n|msi', $fileHeader, $m)) {
            $sig = $m[1];
            $sig = json_decode($sig, true);
            if ($sig === null) {
                return false;
            }

            return !$this->shouldRefreshCache($sig);
        }

        return false;
    }

    protected function cachedRelativeNameToAbsolutePath(string $name): string
    {
        if (basename($this->threeNvFullPath) == $name) {
            return $this->threeNvFullPath;
        }

        return Path::join2($this->configDir, $name);
    }


    /**
     * @param array<string, string> $sig
     * @return bool
     */
    protected function shouldRefreshCache(array $sig): bool
    {
        foreach ($sig as $file => $fileSig) {
            $newSig = Utils::cacheSignature($this->cachedRelativeNameToAbsolutePath($file));
            if ($fileSig != $newSig) {
                return true;
            }
        }
        return false;
    }
}
