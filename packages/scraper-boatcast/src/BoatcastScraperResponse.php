<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper;

use Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperResponse as BoatcastScraperResponseContract;

/**
 * @author shimomo
 */
final class BoatcastScraperResponse implements BoatcastScraperResponseContract
{
    /**
     * @param ?array<array-key, mixed> $value
     */
    public function __construct(private readonly ?array $value = null)
    {
        //
    }

    /**
     * @return ?array<array-key, mixed>
     */
    #[\Override]
    public function getValue(): ?array
    {
        return $this->value;
    }
}
