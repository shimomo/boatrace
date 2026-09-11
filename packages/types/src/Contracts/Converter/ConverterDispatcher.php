<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Converter;

use UnitEnum;

/**
 * @author shimomo
 */
interface ConverterDispatcher extends Converter
{
    /**
     * @param int|float|string|null $value
     * @return ?int
     */
    public function toInt(int|float|string|null $value): ?int;

    /**
     * @param int|float|string|null $value
     * @return int
     */
    public function toIntStrict(int|float|string|null $value): int;

    /**
     * @param int|float|string|null $value
     * @return int|float|string|null
     */
    public function toIntOrReturn(int|float|string|null $value): int|float|string|null;

    /**
     * @param int|float|string|null $value
     * @return ?float
     */
    public function toFloat(int|float|string|null $value): ?float;

    /**
     * @param int|float|string $value
     * @return float
     */
    public function toFloatStrict(int|float|string $value): float;

    /**
     * @param int|float|string|null $value
     * @return int|float|string|null
     */
    public function toFloatOrReturn(int|float|string|null $value): int|float|string|null;

    /**
     * @param int|float|string|null $value
     * @return ?string
     */
    public function toString(int|float|string|null $value): ?string;

    /**
     * @param int|float|string $value
     * @return string
     */
    public function toStringStrict(int|float|string $value): string;

    /**
     * @param int|float|string|null $value
     * @return null
     */
    public function toNull(int|float|string|null $value): null;

    /**
     * @param ?string $value
     * @param non-empty-string $mode
     * @return ?string
     */
    public function toKana(?string $value, string $mode = 'KVas'): ?string;

    /**
     * @param ?string $value
     * @return ?int
     */
    public function toDayNumber(?string $value): ?int;

    /**
     * @param string $value
     * @return string
     */
    public function toCamelCase(string $value): string;

    /**
     * @template TValue
     * @param array<string, TValue> $value
     * @return array<string, TValue>
     */
    public function toCamelCaseKeys(array $value): array;

    /**
     * @template T of \UnitEnum
     * @param callable(): ?T $resolver
     * @return ?T
     */
    public function toEnumOrNull(callable $resolver): ?UnitEnum;
}
