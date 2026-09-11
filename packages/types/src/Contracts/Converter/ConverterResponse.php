<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Converter;

use UnitEnum;

/**
 * @author shimomo
 */
interface ConverterResponse extends Converter
{
    /**
     * @return int|float|string|array<array-key, mixed>|\UnitEnum|null
     */
    public function getValue(): int|float|string|array|UnitEnum|null;
}
