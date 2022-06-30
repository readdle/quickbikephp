<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Config;

/** @internal  */
class CodeGenerator
{
    protected const TAB_WIDTH = 4;
    private string $code = '';

    public function __construct(string $comment = '')
    {
        $this->code .= "<?php\n//this is a generated file, edit config.*.php instead\n//$comment\n";
        $this->code .= 'declare(strict_types=1);'.PHP_EOL;
        $this->code .= 'namespace Readdle\QuickBike\Config;'.PHP_EOL.PHP_EOL.'class GeneratedRuntimeConfig'.PHP_EOL.'{'.PHP_EOL;
    }

    protected static function niceVarExport(mixed $value, bool $newlines = false, int $tabLevel = 1): string
    {
        if (is_array($value)) {
            if (array_is_list($value)) {
                return self::exportListArray($value, $newlines, $tabLevel);
            } else {
                return self::exportAssociativeArray($value, $newlines, $tabLevel);
            }
        }
        return var_export($value, true);
    }

    /**
     * @param array<mixed> $value
     * @param bool $newlines
     * @param int $tabLevel
     * @return string
     */
    protected static function exportListArray(array $value, bool $newlines, int $tabLevel): string
    {
        if ($newlines) {
            $ret = "[\n";
            foreach ($value as $item) {
                $ret .= str_repeat(' ', self::TAB_WIDTH * ($tabLevel + 1));
                $ret .= self::niceVarExport($item) . ",\n";
            }
            $ret .= str_repeat(' ', self::TAB_WIDTH * $tabLevel). ']';
            return $ret;
        } else {
            $ret = '[';
            foreach ($value as $item) {
                $ret .= self::niceVarExport($item) . ', ';
            }
            $ret = substr($ret, 0, -2);
            return $ret . ']';
        }
    }

    protected static function exportAssociativeArray(mixed $value, bool $newlines, int $tabLevel): string
    {
        if ($newlines) {
            $ret = "[\n";

            $max_length = array_reduce(array_keys($value), function ($a, $b) {
                return max($a, strlen(strval($b)));
            }, 0);

            $max_length += 3;

            foreach ($value as $key => $item) {
                $ret .= str_repeat(' ', self::TAB_WIDTH * ($tabLevel + 1));

                $exported = self::niceVarExport($item, false, $tabLevel + 1);
                if (strlen($exported) > 80) {
                    $exported = self::niceVarExport($item, true, $tabLevel + 1);
                }

                $ret .= str_pad(var_export($key, true), $max_length) . ' => ' .
                    $exported . ",\n";
            }

            $ret .= str_repeat(' ', self::TAB_WIDTH * $tabLevel). ']';
            return $ret;
        } else {
            $ret = '[';
            foreach ($value as $key => $item) {
                $ret .= var_export($key, true) . ' => ' . self::niceVarExport($item) . ', ';
            }
            return substr($ret, 0, -2) . ']';
        }
    }

    /**
     * @param array<string, mixed> $constants
     * @return void
     */
    public function addConstants(array $constants): void
    {
        foreach ($constants as $constant => $value) {
            $this->addConstant($constant, $value);
        }
    }

    public function addConstant(string $name, mixed $value): void
    {
        if (!preg_match('|^[A-Z][A-Z_]+[A-Z]$|', $name)) {
            throw new \InvalidArgumentException('bad constant name in a config: '.$name);
        }

        $tab = '    ';
        $line = $tab.'public const '.$name. ' = '. self::niceVarExport($value).";\n";
        if (strlen($line) > 120) {
            $line = $tab.'public const '.$name. ' = '. self::niceVarExport($value, true).";\n";
        }
        $this->code .= $line;
    }

    public function getGeneratedCode(): string
    {
        return $this->code.'}'.PHP_EOL;
    }

    public function writeToFile(string $path): bool
    {
        return !(file_put_contents($path, $this->getGeneratedCode()) === false);
    }
}
