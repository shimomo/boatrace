<?php

declare(strict_types=1);

namespace Boatrace\Support\Normalizer;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Normalizer\NormalizerDispatcher as NormalizerDispatcherContract;

/**
 * @author shimomo
 */
final class NormalizerDispatcher implements NormalizerDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @var array<non-empty-string, bool>
     */
    private const array DEFAULT_OPTIONS = [
        'shouldRemoveAllSpaces' => false,
        'shouldRemoveAllNumbers' => false,
        'shouldRemoveAllNotNumbers' => false,
    ];

    /**
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     */
    public function __construct(private readonly ConverterDispatcherContract $converter)
    {
        //
    }

    /**
     * @param int|float|string|array<array-key, mixed>|null $value
     * @param array<non-empty-string, bool> $options
     * @return int|float|string|array<array-key, mixed>|null
     */
    #[\Override]
    public function normalize(
        int|float|string|array|null $value,
        array $options = []
    ): int|float|string|array|null {
        if (is_int($value) || is_float($value) || is_null($value)) {
            return $value;
        }

        if (is_array($value)) {
            return array_map(
                fn(int|float|string|array|null $item): int|float|string|array|null
                    => $this->normalize($item, $options),
                $value
            );
        }

        if (is_numeric($value)) {
            return str_contains($value, '.')
                ? $this->converter->toFloat($value)
                : $this->converter->toInt($value);
        }

        /** @var array<non-empty-string, bool> $options */
        $options = array_merge(self::DEFAULT_OPTIONS, $this->converter->toCamelCaseKeys($options));

        $value = $this->normalizeSpaces($value, $options);
        $value = $this->normalizeNumbers($value, $options);
        $value = $this->normalizeNotNumbers($value, $options);

        return $value;
    }

    /**
     * @param string $value
     * @param array<non-empty-string, bool> $options
     * @return string
     */
    private function normalizeSpaces(string $value, array $options): string
    {
        $pattern = ($options['shouldRemoveAllSpaces'] ?? false) ? '' : ' ';

        return preg_replace('/\s+/u', $pattern, $value) ?? $value;
    }

    /**
     * @param string $value
     * @param array<non-empty-string, bool> $options
     * @return string
     */
    private function normalizeNumbers(string $value, array $options): string
    {
        if (!($options['shouldRemoveAllNumbers'] ?? false)) {
            return $value;
        }

        return preg_replace('/\d/u', '', $value) ?? $value;
    }

    /**
     * @param string $value
     * @param array<non-empty-string, bool> $options
     * @return string
     */
    private function normalizeNotNumbers(string $value, array $options): string
    {
        if (!($options['shouldRemoveAllNotNumbers'] ?? false)) {
            return $value;
        }

        return preg_replace('/\D/u', '', $value) ?? $value;
    }
}
