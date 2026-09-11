<?php

declare(strict_types=1);

namespace Boatrace\Support\Converter;

use Boatrace\Types\Contracts\Converter\ConverterResponse as ConverterResponseContract;
use UnitEnum;

/**
 * @author shimomo
 */
final class ConverterResponse implements ConverterResponseContract
{
    /**
     * @param int|float|string|array<array-key, mixed>|\UnitEnum|null $value
     */
    public function __construct(
        private readonly int|float|string|array|UnitEnum|null $value = null
    ) {
        //
    }

    /**
     * @return int|float|string|array<array-key, mixed>|\UnitEnum|null
     */
    #[\Override]
    public function getValue(): int|float|string|array|UnitEnum|null
    {
        return $this->value;
    }
}
