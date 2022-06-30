<?php

namespace Readdle\QuickBike\Core;

use Readdle\QuickBike\Config\GeneratedRuntimeConfig;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class Templates
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(Path::views());
        $cache = false;
        if (GeneratedRuntimeConfig::TWIG_CACHE) {
            $cache = Path::cache('twig/');
        }

        $this->twig = new Environment($loader, [
            'cache'            => $cache,
            'strict_variables' => true,
        ]);
    }

    /**
     * @param string $name
     * @param array<string, mixed> $context
     * @param int $statusCode
     * @param array<string, mixed> $headers
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $name, array $context = [], int $statusCode = 200, array $headers = []): Response
    {
        return new Response($this->twig->render($name, $context), $statusCode, $headers);
    }
}
