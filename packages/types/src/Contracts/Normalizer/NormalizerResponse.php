<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Normalizer;

/**
 * @author shimomo
 */
interface NormalizerResponse extends Normalizer
{
    /**
     * @return int|float|string|array<array-key, mixed>|null
     */
    public function getValue(): int|float|string|array|null;
}
