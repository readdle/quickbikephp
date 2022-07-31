<?php
declare(strict_types=1);

namespace Readdle\QuickBike\Test\Core;

use Monolog\Test\TestCase;
use Readdle\QuickBike\Core\Result\DataResult;
use Readdle\QuickBike\Core\Result\FileResult;
use Readdle\QuickBike\Core\Result\JsonResult;
use Readdle\QuickBike\Core\Result\RedirectResult;
use Readdle\QuickBike\Core\Result\ResultInterface;
use Readdle\QuickBike\Core\Result\TemplateResult;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ResultTest extends TestCase
{
    const CT_TEXT_PLAIN = ['content-type' => 'text/plain'];
    const UNIQUE_STRING = 'un1queT3stString';

    public function testResults(): void
    {
        /** @var array<ResultInterface> $results */
        $results = [
            new DataResult(self::UNIQUE_STRING, 200, self::CT_TEXT_PLAIN),
            new JsonResult(['message' => self::UNIQUE_STRING], 200, self::CT_TEXT_PLAIN),
            new RedirectResult('http://localhost:5995/'.self::UNIQUE_STRING, 302, self::CT_TEXT_PLAIN),
            new FileResult('/tmp/'.self::UNIQUE_STRING, self::CT_TEXT_PLAIN),
            new TemplateResult('/tmp/template', ['var' => self::UNIQUE_STRING], 200, self::CT_TEXT_PLAIN),
        ];

        foreach ($results as $result) {
            if (!($result instanceof RedirectResult)) {
                $this->assertEquals(200, $result->getStatusCode());
            }

            $this->assertEquals(self::CT_TEXT_PLAIN, $result->getHeaders());
            $r2 = $result->appendingHeaders(['accept' => 'text/html']);
            $this->assertArrayHasKey('accept', $r2->getHeaders());
            $this->assertCount(2, $r2->getHeaders());

            $r3 = $result->replacingHeaders(['content-type' => 'text/html']);
            $this->assertEquals(['content-type' => 'text/html'], $r3->getHeaders());

            $this->assertStringContainsString(self::UNIQUE_STRING, implode(',', $result->getData()));

            if ($result instanceof DataResult || $result instanceof JsonResult) {
                $resp = $result->createResponse();
                $this->assertStringContainsString(self::UNIQUE_STRING, $resp->getContent());
            }

            if ($result instanceof RedirectResult) {
                $loc = '';
                $resp = $result->createResponse();
                if ($resp instanceof RedirectResponse) {
                    $loc = $resp->getTargetUrl();
                }

                $this->assertStringContainsString(self::UNIQUE_STRING, $loc);
            }
        }
    }
}
