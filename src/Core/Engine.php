<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core;

use AutoRoute\AutoRoute;
use AutoRoute\Exception as AutoRouteException;
use AutoRoute\Route;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Readdle\QuickBike\Auth\Exception\AuthPermissionException;
use Readdle\QuickBike\Auth\Exception\AuthRedirectException;
use Readdle\QuickBike\Core\Result\RedirectResult;
use Readdle\QuickBike\Core\Result\ResultInterface;
use Readdle\QuickBike\Core\Result\DataResult;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Engine
{
    protected readonly LoggerInterface $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public function __construct(
        private readonly DICore $di
    ) {
        $logger = $this->di->get(LoggerInterface::class);
        if ($logger instanceof LoggerInterface) {
            $this->logger = $logger;
        }
    }

    final public function generateResponseForRequest(?Request $request): Response
    {
        try {
            if ($request === null) {
                $request = $this->di->get(Request::class);
            }

            return $this->routeAndRunExceptionHandle($request)->createResponse();
        } catch (\Throwable $e) {
            $this->logger->error('Unhandled exception caught at Engine', ['exception' => $e]);
            return new Response('', 500);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function routeAndRunExceptionHandle(Request $request): ResultInterface
    {
        try {
            return $this->routeAndRun($request);
        } catch (AuthRedirectException $e) {
            return new RedirectResult('/login');
        } catch (AuthPermissionException $e) {
            return $this->handlePermissionException();
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function handlePermissionException(): RedirectResult
    {
        $session = $this->di->get(Session::class);

        $session->start();
        $session->invalidate();
        $session->getFlashBag()->set('error', 'user permission error, please relogin');
        return new RedirectResult('/login');
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function routeAndRun(Request $request): ResultInterface
    {
        $router = $this->di->get(AutoRoute::class)->getRouter();
        $route = $router->route($request->getMethod(), $request->getPathInfo());
        return $this->handleAutoRoute($route);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function handleAutoRoute(Route $route): ResultInterface
    {
        if ($route->error === null) {
            $action = $this->di->get($route->class);
            $method = $route->method;
            $arguments = $route->arguments;
            return $action->$method(...$arguments);
        } else {
            return $this->handleAutoRouteException($route);
        }
    }

    private function handleAutoRouteException(Route $route): ResultInterface
    {
        $result = match ($route->error) {
            AutoRouteException\InvalidArgument::class  => new DataResult('Bad Request', 400),
            AutoRouteException\NotFound::class         => new DataResult('Not Found', 404),
            AutoRouteException\MethodNotAllowed::class => new DataResult('Method Not Allowed', 405),
            default                                    => new DataResult('Server Error', 500),
        };

        $this->logger->notice($result->text, [
            'className' => $route->class,
            'method'    => $route->method,
            'arguments' => $route->arguments,
            'messages'  => $route->messages,
            'error'     => $route->error
        ]);

        return $result;
    }
}
