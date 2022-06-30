<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Config;

use Readdle\QuickBike\Core\Exception\ConfigBuilderException;
use Readdle\QuickBike\Core\Path;
use ThreeEncr\TokenCrypt;

/** @internal  */
class RealConfigBuilder
{
    public function __construct(
        protected readonly string $envFilePath,
        protected readonly string $configDir,
        protected readonly string $runtimeConfigPath
    ) {
    }

    /**
     * @throws ConfigBuilderException
     */
    public function build(): void
    {
        $cache = new CacheWriter();
        $env = Environment::loadFromFile($this->envFilePath);
        $cache->addFile($this->envFilePath);

        $config = $this->loadConfigForEnvironment($env->name, $cache);
        $config = $this->loadParentConfigs($config, $cache);
        $config = $this->decryptTokens($config, $env->tokenCrypt);
        $config = $this->expandValues($config);

        $sig = 'sig:'.$cache->getJSONCacheLine();
        $codeGen = new CodeGenerator($sig);
        $codeGen->addConstants($config);
        $codeGen->writeToFile($this->runtimeConfigPath);
    }


    /**
     * @param string $name
     * @param CacheWriter $cache
     * @return array<string, mixed>
     * @throws ConfigBuilderException
     */
    private function loadConfigForEnvironment(string $name, CacheWriter $cache): array
    {
        $filePath = Path::join($this->configDir, 'config.'.$name.'.php');

        if (!file_exists($filePath)) {
            throw new ConfigBuilderException("cannot load config for $name");
        }

        if ($cache->containsFile($filePath)) {
            throw new ConfigBuilderException('recursive config loading not supported');
        }

        $cache->addFile($filePath);

        return include $filePath;
    }


    /**
     * @param array<string, mixed> $config
     * @param CacheWriter $cache
     * @return array<string, mixed>
     * @throws ConfigBuilderException
     */
    private function loadParentConfigs(array $config, CacheWriter $cache): array
    {
        $parentKey = '_parent';

        if (!array_key_exists($parentKey, $config)) {
            return $config;
        }
        $parentFile = $config[$parentKey];
        if (!is_string($parentFile)) {
            throw new ConfigBuilderException('_parent config key must be string');
        }

        unset($config[$parentKey]);
        $parentContent = $this->loadConfigForEnvironment($parentFile, $cache);
        $newConfig = array_replace_recursive($parentContent, $config);
        return $this->loadParentConfigs($newConfig, $cache);
    }


    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    protected function expandValues(array $config): array
    {
        return Utils::arrayMapStringLeaves($config, fn ($x) => ValueExpander::expandValue($x));
    }

    /**
     * @param array<string, mixed> $encConfig
     * @param TokenCrypt $tokenCrypt
     * @return array<string, mixed>
     */
    protected function decryptTokens(array $encConfig, TokenCrypt $tokenCrypt): array
    {
        return Utils::arrayMapStringLeaves($encConfig, function ($str) use ($tokenCrypt) {
            $r = $tokenCrypt->decrypt3ncr($str);
            if ($r === null) {
                return '.unable to decrypt.';
            }
            return $r;
        });
    }
}
