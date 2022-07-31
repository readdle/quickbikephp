<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Result;

use Symfony\Component\HttpFoundation\Response;

interface ResultInterface
{
    /**
     * @return array<string, string>
     */
    public function getData(): array;
    public function getStatusCode(): int;

    /**
     * @return array<string, mixed>
     */
    public function getHeaders(): array;

    /**
     * @param array<string, mixed> $headers
     * @return $this
     */
    public function appendingHeaders(array $headers): self;

    /**
     * @param array<string, mixed> $headers
     * @return $this
     */
    public function replacingHeaders(array $headers): self;

    public function createResponse(): Response;
}
