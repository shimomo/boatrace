<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Browser;

use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
interface BrowserResponse extends Browser
{
    /**
     * @return ?\Symfony\Component\BrowserKit\HttpBrowser
     */
    public function getValue(): ?HttpBrowser;
}
