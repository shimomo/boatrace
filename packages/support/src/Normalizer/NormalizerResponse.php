<?php

declare(strict_types=1);

namespace Boatrace\Support\Normalizer;

use Boatrace\Types\Contracts\Normalizer\NormalizerResponse as NormalizerResponseContract;

/**
 * @author shimomo
 */
final class NormalizerResponse implements NormalizerResponseContract
{
    /**
     * @param int|float|string|array<array-key, mixed>|null $value
     */
    public function __construct(private readonly int|float|string|array|null $value = null)
    {
        //
    }

    /**
     * @return int|float|string|array<array-key, mixed>|null
     */
    #[\Override]
    public function getValue(): int|float|string|array|null
    {
        return $this->value;
    }
}
