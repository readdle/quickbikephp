<?php

namespace Readdle\QuickBike\Core\Result;

class TemplateResult implements ResultInterface
{
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
}
