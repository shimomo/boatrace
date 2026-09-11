<?php

declare(strict_types=1);

namespace Boatrace\Scraper;

use Boatrace\Types\Contracts\Scraper\ScraperResponse as ScraperResponseContract;

/**
 * @author shimomo
 */
final class ScraperResponse implements ScraperResponseContract
{
    /**
     * @param float|bool|array<array-key, mixed>|null $value
     */
    public function __construct(private readonly float|bool|array|null $value = null)
    {
        //
    }

    /**
     * @return float|bool|array<array-key, mixed>|null
     */
    #[\Override]
    public function getValue(): float|bool|array|null
    {
        return $this->value;
    }
}
