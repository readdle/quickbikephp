<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Result;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

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

    public function createResponse(): Response
    {
        return new Response($this->text, $this->statusCode, $this->headers);
    }

    public function appendingHeaders(array $headers): DataResult
    {
        return $this->replacingHeaders(array_merge($this->headers, $headers));
    }

    public function replacingHeaders(array $headers): DataResult
    {
        return new DataResult($this->text, $this->statusCode, $headers);
    }
}
