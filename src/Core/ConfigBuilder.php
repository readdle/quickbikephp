<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core;

use Readdle\QuickBike\Core\Config\CacheChecker;
use Readdle\QuickBike\Core\Config\RealConfigBuilder;
use Readdle\QuickBike\Core\Exception\ConfigBuilderException;

class ConfigBuilder
{
    protected const QUICKBIKE_NO_CONFIG_UPDATE = 'QUICKBIKE_NO_CONFIG_UPDATE';

    protected const CONFIG_DIR = 'config';
    protected const THREE_NV_FILE = '.3nv';
    private const RUNTIME_CONFIG = 'generated/GeneratedRuntimeConfig.php';

    protected readonly string $runtimeConfigPath;
    protected readonly string $configDir;
    protected readonly string $f3nv;

    public function __construct()
    {
        $this->configDir = Path::root(self::CONFIG_DIR);
        $this->runtimeConfigPath = Path::join2($this->configDir, self::RUNTIME_CONFIG);
        $this->f3nv = Path::root(self::THREE_NV_FILE);
    }

    /**
     * @throws ConfigBuilderException
     */
    public function buildIfNeeded(): void
    {
        if (getenv(self::QUICKBIKE_NO_CONFIG_UPDATE)) {
            return;
        }

        if (class_exists('Readdle\QuickBike\Config\GeneratedRuntimeConfig', false)) {
            throw new \LogicException('GeneratedRuntimeConfig was loaded before generating class');
        }

        $checker = new CacheChecker($this->runtimeConfigPath, $this->f3nv, $this->configDir);
        if (!$checker->checkIfConfigActual()) {
            $this->generate();
        }
    }

    /**
     * @throws ConfigBuilderException
     */
    public function generate(): void
    {
        $builder = new RealConfigBuilder(
            envFilePath: $this->f3nv,
            configDir: $this->configDir,
            runtimeConfigPath: $this->runtimeConfigPath
        );

        $builder->build();
    }
}
