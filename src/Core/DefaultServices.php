<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core;

use AutoRoute\AutoRoute;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\TagProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;
use PDOException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Readdle\Database\FQDB;
use Readdle\Database\FQDBProvider;
use Readdle\QuickBike\Config\CodeConfig;
use Readdle\QuickBike\Config\GeneratedRuntimeConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/** @internal  */
class DefaultServices extends AbstractDIContainer
{
    protected const DI_INTERFACE_MAP = [
        LoggerInterface::class => Logger::class
    ];

    protected const DI_TYPE_MAP = [
        Logger::class    => 'logger',
        Request::class   => 'request',
        FQDB::class      => 'fqdb',
        Session::class   => 'session',
        AutoRoute::class => 'autoRoute'
    ];

    public function request() : Request
    {
        return Request::createFromGlobals();
    }

    public function autoRoute(): AutoRoute
    {
        return new AutoRoute(
            namespace: CodeConfig::HTTP_ACTIONS_NAMESPACE,
            directory: CodeConfig::HTTP_ACTIONS_PATH
        );
    }

    public function fqdb() : FQDB
    {
        return FQDBProvider::dbWithDSN(
            GeneratedRuntimeConfig::DB_DSN,
            GeneratedRuntimeConfig::DB_USER,
            GeneratedRuntimeConfig::DB_PASSWORD
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function session() : Session
    {
        /**
         * @var FQDB $fqdb
         */
        $fqdb = $this->get(FQDB::class);

        $sessionHandler = new PdoSessionHandler($fqdb->getPdo());
        $sessionStorage = new NativeSessionStorage([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
        ], $sessionHandler);
        try {
            $sessionHandler->createTable();
        } catch (PDOException) {
            // table exists
        }
        $session = new Session($sessionStorage);
        $session->setName('gdpr-strictly-necessary-id');
        return $session;
    }

    protected function logger() : Logger
    {
        $logger = new Logger('application');
        $logger->pushProcessor(new UidProcessor());

        if (php_sapi_name() === 'cli') {
            $logger->pushProcessor(new TagProcessor(['cli']));
        } elseif (php_sapi_name() == 'fpm-fcgi') {
            $logger->pushProcessor(new WebProcessor());
        }

        $handler = new StreamHandler(GeneratedRuntimeConfig::LOG_FILE);
        if (GeneratedRuntimeConfig::LOG_JSON) {
            $jsonFormatter = new JsonFormatter();
            $jsonFormatter->includeStacktraces(true);
            $handler->setFormatter($jsonFormatter);
        } else {
            $lineFormatter = new JsonFormatter();
            $lineFormatter->includeStacktraces(true);
            $handler->setFormatter($lineFormatter);
        }

        $logger->pushHandler($handler);
        return $logger;
    }
}
