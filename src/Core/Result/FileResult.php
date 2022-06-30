<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Result;

use JetBrains\PhpStorm\ArrayShape;

class FileResult implements ResultInterface
{
    public function __construct(
        public string $fileName
    ) {
    }

    /**
     * @return array<string, string>
     */
    #[ArrayShape(['file' => 'string'])]
    public function getData(): array
    {
        return ['file' => $this->fileName];
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        return [];
    }
}
