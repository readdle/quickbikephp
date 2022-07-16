<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Core\Result;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class FileResult implements ResultInterface
{

    /**
     * @param string $fileName
     * @param array<string, mixed> $headers
     * @param ?string $contentDisposition
     */
    public function __construct(
        public string $fileName,
        public readonly array $headers = [],
        public readonly ?string $contentDisposition = null
    ) {
    }

    /**
     * @return array<string, ?string>
     */
    #[ArrayShape(['file' => 'string', 'contentDisposition' => '?string'])]
    public function getData(): array
    {
        return ['file' => $this->fileName, 'contentDisposition' => $this->contentDisposition];
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
        return $this->headers;
    }

    public function createResponse(): Response
    {
        return new BinaryFileResponse(
            $this->fileName,
            200,
            $this->headers,
            true,
            $this->contentDisposition
        );
    }
}
