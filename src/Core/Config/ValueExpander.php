<?php

namespace Readdle\QuickBike\Core\Config;

use Readdle\QuickBike\Core\Path;

/** @internal  */
class ValueExpander
{
    private const FILE_NOT_EXISTS_ERROR = 'expansion error: file not exists';
    private const BAD_JSON_FILE_ERROR = 'expansion error: bad JSON in file';
    private const UNSUPPORTED_OPERATOR = 'expansion error: unsupported operator';


    public static function expandValue(string $value): string
    {
        $fileValue = self::expandFileValue($value);
        if ($fileValue !== $value) {
            return $fileValue;
        }
        return self::expandEnvValue($value);
    }

    protected static function expandEnvValue(string $value): string
    {
        return preg_replace_callback('/%env\(([A-Z\da-z_.\/\-]+)\)%/', function ($m) {
            $arg = $m[1];
            $env = getenv($arg);
            return ($env !== false) ? $env : $arg;
        }, $value);
    }

    protected static function expandFileValue(string $value): string
    {
        if (preg_match('/^%(file|json_file)\(([A-Z\da-z_.\/\-]+)\)%$/', $value, $m)) {
            $op = $m[1];
            $arg = $m[2];
            if ($op === 'file') {
                return self::loadFile($arg);
            } elseif ($op == 'json_file') {
                return self::loadJsonFile($arg);
            }
            return self::UNSUPPORTED_OPERATOR;
        }
        return $value;
    }

    private static function loadJsonFile(string $relativeOrAbsolutePath): mixed
    {
        $content = self::loadFile($relativeOrAbsolutePath);
        if ($content === self::FILE_NOT_EXISTS_ERROR) {
            return self::FILE_NOT_EXISTS_ERROR;
        }
        $decoded = json_decode($content, true);
        if ($decoded === null) {
            return self::BAD_JSON_FILE_ERROR;
        }
        return $decoded;
    }

    private static function loadFile(string $relativeOrAbsolutePath): string
    {
        $oldCwd = getcwd();
        chdir(Path::root('config'));
        if (file_exists($relativeOrAbsolutePath)) {
            $content = trim(file_get_contents($relativeOrAbsolutePath));
            chdir($oldCwd);
            return $content;
        }

        chdir($oldCwd);
        return self::FILE_NOT_EXISTS_ERROR;
    }
}
