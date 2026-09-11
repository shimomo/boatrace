<?php

declare(strict_types=1);

namespace Boatrace\Support\Converter;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher as TrimmerDispatcherContract;
use UnitEnum;
use ValueError;

/**
 * @author shimomo
 */
final class ConverterDispatcher implements ConverterDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @param \Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher $trimmer
     */
    public function __construct(private readonly TrimmerDispatcherContract $trimmer)
    {
        //
    }

    /**
     * @param int|float|string|null $value
     * @return ?int
     */
    #[\Override]
    public function toInt(int|float|string|null $value): ?int
    {
        return $value !== null ? (int) $value : null;
    }

    /**
     * @param int|float|string|null $value
     * @return int
     */
    #[\Override]
    public function toIntStrict(int|float|string|null $value): int
    {
        return (int) $value;
    }

    /**
     * @param int|float|string|null $value
     * @return int|float|string|null
     */
    #[\Override]
    public function toIntOrReturn(int|float|string|null $value): int|float|string|null
    {
        return is_numeric($value) ? (int) $value : $value;
    }

    /**
     * @param int|float|string|null $value
     * @return ?float
     */
    #[\Override]
    public function toFloat(int|float|string|null $value): ?float
    {
        return $value !== null ? (float) $value : null;
    }

    /**
     * @param int|float|string $value
     * @return float
     */
    #[\Override]
    public function toFloatStrict(int|float|string $value): float
    {
        return (float) $value;
    }

    /**
     * @param int|float|string|null $value
     * @return int|float|string|null
     */
    #[\Override]
    public function toFloatOrReturn(int|float|string|null $value): int|float|string|null
    {
        return is_numeric($value) ? (float) $value : $value;
    }

    /**
     * @param int|float|string|null $value
     * @return ?string
     */
    #[\Override]
    public function toString(int|float|string|null $value): ?string
    {
        return $value !== null ? (string) $value : null;
    }

    /**
     * @param int|float|string $value
     * @return string
     */
    #[\Override]
    public function toStringStrict(int|float|string $value): string
    {
        return (string) $value;
    }

    /**
     * @param int|float|string|null $value
     * @return null
     */
    #[\Override]
    public function toNull(int|float|string|null $value): null
    {
        return null;
    }

    /**
     * @param ?string $value
     * @param non-empty-string $mode
     * @return ?string
     */
    #[\Override]
    public function toKana(?string $value, string $mode = 'KVas'): ?string
    {
        return $value !== null ? mb_convert_kana($value, $mode, 'UTF-8') : null;
    }

    /**
     * @param ?string $value
     * @return ?int
     */
    #[\Override]
    public function toDayNumber(?string $value): ?int
    {
        return $this->toInt($this->toKana($this->trimmer->trimOrNull($value)));
    }

    /**
     * @param string $value
     * @return string
     */
    #[\Override]
    public function toCamelCase(string $value): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
    }

    /**
     * @template TValue
     * @param array<string, TValue> $value
     * @return array<string, TValue>
     */
    #[\Override]
    public function toCamelCaseKeys(array $value): array
    {
        $response = [];

        foreach ($value as $key => $item) {
            $response[$this->toCamelCase($key)] = $item;
        }

        return $response;
    }

    /**
     * @template T of \UnitEnum
     * @param callable(): ?T $resolver
     * @return ?T
     */
    #[\Override]
    public function toEnumOrNull(callable $resolver): ?UnitEnum
    {
        try {
            return $resolver();
        } catch (ValueError) {
            return null;
        }
    }
}
