<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @author shimomo
 */
final class MockBrowser
{
    /**
     * @param non-empty-string ...$fixtures
     * @return \Symfony\Component\BrowserKit\HttpBrowser
     */
    public static function create(string ...$fixtures): HttpBrowser
    {
        $responses = array_map(
            fn(string $fixture): MockResponse => new MockResponse(
                (string) file_get_contents(__DIR__ . '/fixtures/' . $fixture),
                ['response_headers' => ['content-type' => 'text/html; charset=utf-8']]
            ),
            $fixtures
        );

        return new HttpBrowser(new MockHttpClient($responses));
    }
}
