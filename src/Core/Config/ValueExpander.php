<?php

namespace Readdle\QuickBike\Core\Config;

use Readdle\QuickBike\Core\Path;

/** @internal  */
class ValueExpander
{
    private const FILE_NOT_EXISTS_ERROR = 'expansion error: file not exists';
    private const BAD_JSON_FILE_ERROR = 'expansion error: bad JSON in file';
    private const UNSUPPORTED_OPERATOR = 'expansion error: unsupported operator';


    public static function expandValue(string $value): mixed
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

    /**
     * @param string $value
     * @return mixed could return anything, because of json_file
     */
    protected static function expandFileValue(string $value): mixed
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

    private static function loadFileAtAbsolutePath(string $absolutePath): string
    {
        if (file_exists($absolutePath)) {
            $data = file_get_contents($absolutePath);
            if ($data === false) {
                return self::FILE_NOT_EXISTS_ERROR;
            }
            return trim($data);
        }
        return self::FILE_NOT_EXISTS_ERROR;
    }

    private static function resolvePathRelativeToConfig(string $path): string
    {
        if (str_starts_with($path, '/')) {
            return $path; // absolute path, UNIX
        }

        $noSlashes = !str_contains($path, '/');

        // trivial paths like ./test.config
        if (str_starts_with($path, './')) {
            $restPath = substr($path, 2);
            if (!str_contains($restPath, '/')) {
                $path = $restPath;
                $noSlashes = true;
            }
        }

        if ($noSlashes) {
            return Path::root('config', $path);
        }

        $oldCwd = getcwd();
        chdir(Path::root('config'));
        $absolutePath = realpath($path);
        chdir($oldCwd);

        return $absolutePath;
    }

    private static function loadFile(string $relativeOrAbsolutePath): string
    {
        return self::loadFileAtAbsolutePath(self::resolvePathRelativeToConfig($relativeOrAbsolutePath));
    }
}
