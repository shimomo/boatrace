<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Browser;

use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
interface BrowserDispatcher extends Browser
{
    /**
     * @param array<non-empty-string, non-empty-string> $serverParameters
     * @return \Symfony\Component\BrowserKit\HttpBrowser
     */
    public function create(array $serverParameters = []): HttpBrowser;

    /**
     * @return array<non-empty-string, float>
     */
    public function getHttpClientOptions(): array;
}
