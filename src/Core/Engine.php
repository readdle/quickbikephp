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
use Readdle\QuickBike\Core\Result\FileResult;
use Readdle\QuickBike\Core\Result\JsonResult;
use Readdle\QuickBike\Core\Result\RedirectResult;
use Readdle\QuickBike\Core\Result\ResultInterface;
use Readdle\QuickBike\Core\Result\TemplateResult;
use Readdle\QuickBike\Core\Result\DataResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Engine
{
    protected readonly LoggerInterface $logger;

    public function __construct(
        private readonly DICore $di
    ) {
        $logger = $this->di->get(LoggerInterface::class);
        if ($logger instanceof LoggerInterface) {
            $this->logger = $logger;
        }
    }


    public function generateResponseForRequest(?Request $request): Response
    {
        try {
            if ($request === null) {
                $request = $this->di->get(Request::class);
            }

            $router = $this->di->get(AutoRoute::class)->getRouter();
            $route = $router->route($request->getMethod(), $request->getPathInfo());
            $result = $this->handleRoute($route);

            return $this->responseForResult($result);
        } catch (\Throwable $e) {
            $this->logger->error('Unhandled exception caught at Engine', ['exception' => $e]);
            return new Response('', 500);
        }
    }


    /**
     * @throws SyntaxError
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    protected function responseForResult(ResultInterface $result): Response
    {
        switch (true) {
            case $result instanceof JsonResult:
                return new JsonResponse($result->getData(), $result->getStatusCode(), $result->getHeaders());

            case $result instanceof TemplateResult:
                /** @var Templates $templates */
                $templates = $this->di->get(Templates::class);
                return $templates->render($result->templateName, $result->getData());

            case $result instanceof RedirectResult:
                return new RedirectResponse($result->url, $result->statusCode, $result->getHeaders());

            case $result instanceof DataResult:
                return new Response($result->text, $result->statusCode, $result->getHeaders());

            case $result instanceof FileResult:
                return new BinaryFileResponse(
                    $result->fileName,
                    200,
                    [],
                    true,
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT
                );
        }

        throw new \LogicException('Unsupported Result Type');
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

    protected function handleRoute(Route $route): ResultInterface
    {
        if ($route->error === null) {
            try {
                $action = $this->di->get($route->class);
            } catch (AuthRedirectException $e) {
                return new RedirectResult('/login');
            } catch (AuthPermissionException $e) {
                return $this->handlePermissionException();
            } catch (\ReflectionException $e) {
                return new RedirectResult('ReflectionException');
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface|Exception\DIException $e) {
                return new DataResult('Server Error', 500);
            }

            $method = $route->method;
            $arguments = $route->arguments;
            return $action->$method(...$arguments);
        } else {
            return $this->handleRouteException($route);
        }
    }

    protected function handleRouteException(Route $route): ResultInterface
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
