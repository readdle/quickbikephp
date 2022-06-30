<?php

namespace Readdle\QuickBike\Core\Result;

use JetBrains\PhpStorm\ArrayShape;

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
}
