<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Browser;

use Symfony\Component\BrowserKit\Response;

/**
 * @author shimomo
 */
interface BrowserObserver extends Browser
{
    /**
     * @param string $method
     * @param string $uri
     * @param \Symfony\Component\BrowserKit\Response $response
     * @param array<non-empty-string, array{dur: ?float, desc: ?string}> $timings
     * @return void
     */
    public function observe(string $method, string $uri, Response $response, array $timings): void;
}
