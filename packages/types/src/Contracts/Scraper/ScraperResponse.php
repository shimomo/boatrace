<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Scraper;

/**
 * @author shimomo
 */
interface ScraperResponse extends Scraper
{
    /**
     * @return float|bool|array<array-key, mixed>|null
     */
    public function getValue(): float|bool|array|null;
}
