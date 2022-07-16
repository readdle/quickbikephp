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


    public function createResponse(): Response;
}
