<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Normalizer;

/**
 * @author shimomo
 */
interface NormalizerDispatcher extends Normalizer
{
    /**
     * @param int|float|string|array<array-key, mixed>|null $value
     * @param array<non-empty-string, bool> $options
     * @return int|float|string|array<array-key, mixed>|null
     */
    public function normalize(
        int|float|string|array|null $value,
        array $options = []
    ): int|float|string|array|null;
}
