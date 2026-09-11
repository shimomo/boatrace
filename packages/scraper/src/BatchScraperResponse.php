<?php

declare(strict_types=1);

namespace Boatrace\Scraper;

use Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse as BatchScraperResponseContract;

/**
 * @author shimomo
 */
final class BatchScraperResponse implements BatchScraperResponseContract
{
    /**
     * @param bool|array<array-key, mixed>|null $value
     */
    public function __construct(private readonly bool|array|null $value = null)
    {
        //
    }

    /**
     * @return bool|array<array-key, mixed>|null
     */
    #[\Override]
    public function getValue(): bool|array|null
    {
        return $this->value;
    }
}
