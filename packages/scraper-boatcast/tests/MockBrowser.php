<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @author shimomo
 */
final class MockBrowser
{
    /**
     * @param ?non-empty-string $fixture
     * @param int $statusCode
     * @return \Symfony\Component\BrowserKit\HttpBrowser
     */
    public static function create(?string $fixture, int $statusCode = 200): HttpBrowser
    {
        return self::createSequence([[$fixture, $statusCode]]);
    }

    /**
     * @param list<array{0: ?string, 1: int, 2?: array<non-empty-string, string>}> $responses
     * @return \Symfony\Component\BrowserKit\HttpBrowser
     */
    public static function createSequence(array $responses): HttpBrowser
    {
        $mockResponses = array_map(
            fn(array $response): MockResponse => new MockResponse(
                $response[0] !== null ? (string) file_get_contents(__DIR__ . '/fixtures/' . $response[0]) : '',
                [
                    'http_code' => $response[1],
                    'response_headers' => [
                        'content-type' => 'text/plain; charset=utf-8',
                        ...($response[2] ?? []),
                    ],
                ]
            ),
            $responses
        );

        return new HttpBrowser(new MockHttpClient($mockResponses));
    }
}
