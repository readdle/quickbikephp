<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Result;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class RedirectResult implements ResultInterface
{
    /**
     * @param string $url
     * @param int $statusCode
     * @param array<string, mixed> $headers
     */
    public function __construct(
        public readonly string $url,
        public readonly int $statusCode = 302,
        public readonly array $headers = []
    ) {
    }

    #[ArrayShape(['url' => 'string'])]
    public function getData(): array
    {
        return ['url' => $this->url];
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function createResponse(): Response
    {
        return new RedirectResponse($this->url, $this->statusCode, $this->headers);
    }
}
