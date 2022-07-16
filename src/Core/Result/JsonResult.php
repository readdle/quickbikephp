<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Result;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResult implements ResultInterface
{

    /**
     * @param array<mixed> $data
     * @param int $statusCode
     * @param array<string, mixed> $headers
     */
    public function __construct(
        public readonly array $data,
        public readonly int $statusCode = 200,
        public readonly array $headers = []
    ) {
    }

    public function getData(): array
    {
        return $this->data;
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
        return new JsonResponse($this->getData(), $this->getStatusCode(), $this->getHeaders());
    }
}
