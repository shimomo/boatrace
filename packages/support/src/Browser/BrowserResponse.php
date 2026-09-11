<?php

declare(strict_types=1);

namespace Boatrace\Support\Browser;

use Boatrace\Types\Contracts\Browser\BrowserResponse as BrowserResponseContract;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class BrowserResponse implements BrowserResponseContract
{
    /**
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $value
     */
    public function __construct(private readonly ?HttpBrowser $value = null)
    {
        //
    }

    /**
     * @return ?\Symfony\Component\BrowserKit\HttpBrowser
     */
    #[\Override]
    public function getValue(): ?HttpBrowser
    {
        return $this->value;
    }
}
