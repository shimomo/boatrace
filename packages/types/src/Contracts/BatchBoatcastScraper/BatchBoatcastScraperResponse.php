<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\BatchBoatcastScraper;

/**
 * @author shimomo
 */
interface BatchBoatcastScraperResponse extends BatchBoatcastScraper
{
    /**
     * @return bool|array<array-key, mixed>|null
     */
    public function getValue(): bool|array|null;
}
