<?php

namespace Readdle\QuickBike\Core\Result;

use JetBrains\PhpStorm\ArrayShape;

class DataResult implements ResultInterface
{
    /**
     * @param string $text
     * @param int $statusCode
     * @param array<string, mixed> $headers
     */
    public function __construct(
        public readonly string $text,
        public readonly int $statusCode = 200,
        public readonly array $headers = []
    ) {
    }

    /**
     * @return array<string, string>
     */
    #[ArrayShape(['text' => 'string'])]
    public function getData(): array
    {
        return ['text' => $this->text];
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
}
