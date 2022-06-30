<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Config;

/** @internal  */
class CacheChecker
{
    public function __construct(
        protected readonly string $generatedPath
    ) {
    }


    public function checkIfConfigActual(): bool
    {
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

    /**
     * @param array<string, string> $sig
     * @return bool
     */
    protected function shouldRefreshCache(array $sig): bool
    {
        foreach ($sig as $file => $fileSig) {
            $newSig = Utils::cacheSignature($file);
            if ($fileSig != $newSig) {
                return true;
            }
        }
        return false;
    }
}
