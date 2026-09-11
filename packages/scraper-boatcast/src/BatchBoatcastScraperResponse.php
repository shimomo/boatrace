<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper;

use Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse as BatchBoatcastScraperResponseContract;

/**
 * @author shimomo
 */
final class BatchBoatcastScraperResponse implements BatchBoatcastScraperResponseContract
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
