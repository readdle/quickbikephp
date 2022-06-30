<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core;

use LogicException;

class ScriptUtils
{
    /**
     * @param string $phpFile - always pass __FILE__ here
     * @return bool
     */
    public static function shouldRunScriptPidCheck(string $phpFile): bool
    {
        $file = basename($phpFile);
        $extensionlessPath = preg_replace('/\.php$/', '', $phpFile);
        $pidFilePath = $extensionlessPath . '.pid';

        if (file_exists($extensionlessPath . '.stop')) {
            return false;
        }

        @$pid = (int)file_get_contents($pidFilePath);

        if ($pid !== 0) {
            @$pout = file_get_contents('/proc/' . $pid . '/cmdline');

            if (is_string($pout) && str_contains($pout, $file)) {
                return false;
            }
        }

        if (file_put_contents($pidFilePath, getmypid()) === false) {
            throw new LogicException('unable to save pid to ' . $pidFilePath);
        }

        return true;
    }
}
