<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Browser;

use Boatrace\Types\Contracts\Browser\BrowserObserver as BrowserObserverContract;
use Symfony\Component\BrowserKit\Response;

/**
 * @author shimomo
 */
final class RecordingBrowserObserver implements BrowserObserverContract
{
    /**
     * @var list<array{
     *     method: string,
     *     uri: string,
     *     statusCode: int,
     *     content: string,
     *     timings: array<non-empty-string, array{dur: ?float, desc: ?string}>,
     * }>
     */
    public array $calls = [];

    /**
     * @param string $method
     * @param string $uri
     * @param \Symfony\Component\BrowserKit\Response $response
     * @param array<non-empty-string, array{dur: ?float, desc: ?string}> $timings
     * @return void
     */
    #[\Override]
    public function observe(string $method, string $uri, Response $response, array $timings): void
    {
        $this->calls[] = [
            'method' => $method,
            'uri' => $uri,
            'statusCode' => $response->getStatusCode(),
            'content' => $response->getContent(),
            'timings' => $timings,
        ];
    }
}
