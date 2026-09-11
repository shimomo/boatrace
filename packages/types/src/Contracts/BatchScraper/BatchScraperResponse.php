<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\BatchScraper;

/**
 * @author shimomo
 */
interface BatchScraperResponse extends BatchScraper
{
    /**
     * @return bool|array<array-key, mixed>|null
     */
    public function getValue(): bool|array|null;
}
