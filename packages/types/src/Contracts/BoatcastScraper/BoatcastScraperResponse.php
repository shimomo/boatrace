<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\BoatcastScraper;

/**
 * @author shimomo
 */
interface BoatcastScraperResponse extends BoatcastScraper
{
    /**
     * @return ?array<array-key, mixed>
     */
    public function getValue(): ?array;
}
