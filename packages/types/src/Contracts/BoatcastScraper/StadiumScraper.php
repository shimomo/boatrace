<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\BoatcastScraper;

use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
interface StadiumScraper extends BoatcastScraper
{
    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, non-empty-string>
     */
    public function scrape(
        DateTimeInterface|string $date,
        ?HttpBrowser $httpBrowser = null
    ): array;
}
