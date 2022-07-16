<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Result;

use Readdle\QuickBike\Core\Templates;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TemplateResult implements ResultInterface
{
    protected static ?Templates $templates = null;

    /**
     * @param string $templateName
     * @param array<string, string> $data
     * @param int $statusCode
     * @param array<string, mixed> $headers
     */
    public function __construct(
        public readonly string $templateName,
        public readonly array $data,
        public readonly int $statusCode = 200,
        public readonly array $headers = []
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function createResponse(): Response
    {
        if (self::$templates === null) {
            self::$templates = new Templates();
        }

        return self::$templates->render($this->templateName, $this->data, $this->statusCode, $this->headers);
    }
}
