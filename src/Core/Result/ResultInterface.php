<?php

namespace Readdle\QuickBike\Core\Result;

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
}
