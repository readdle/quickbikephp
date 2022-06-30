<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Config;

use Readdle\QuickBike\Core\Exception\ConfigBuilderException;
use ThreeEncr\TokenCrypt;
use ThreeEncr\TokenCryptException;

/** @internal  */
class Environment
{
    public function __construct(
        public readonly string     $name,
        public readonly TokenCrypt $tokenCrypt
    ) {
    }


    /**
     * @throws ConfigBuilderException
     */
    public static function loadFromFile(string $filePath): Environment
    {
        if (!file_exists($filePath)) {
            throw new ConfigBuilderException('env file not exists: '.$filePath);
        }

        $content = trim(file_get_contents($filePath));
        $lines = explode("\n", $content);
        if (count($lines) < 2) {
            throw new ConfigBuilderException('env file must contain 2 lines');
        }
        $envName = trim($lines[0]);
        if (strlen($envName) < 1) {
            throw new ConfigBuilderException('env name is empty');
        }

        $secret = trim($lines[1]);
        try {
            $tokenCrypt = TokenCrypt::createWithSecretLine($secret);
        } catch (TokenCryptException $e) {
            throw new ConfigBuilderException('.env secret error: '.$e->getMessage());
        }

        return new Environment($envName, $tokenCrypt);
    }
}
