<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Normalizer;

/**
 * @author shimomo
 */
final class NormalizerDataProvider
{
    /**
     * @return non-empty-list<array{
     *     arguments: int|float|string|null,
     *     expected: int|float|string|null,
     * }>
     */
    public static function normalizeProvider(): array
    {
        return [
            ['arguments' => null, 'expected' => null],
            ['arguments' => 1, 'expected' => 1],
            ['arguments' => 1.5, 'expected' => 1.5],
            ['arguments' => '1', 'expected' => 1],
            ['arguments' => '1.5', 'expected' => 1.5],
            ['arguments' => '競艇', 'expected' => '競艇'],
            ['arguments' => "競艇\n\tレース", 'expected' => '競艇 レース'],
            ['arguments' => '競艇  レース', 'expected' => '競艇 レース'],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     arguments: non-empty-string,
     *     options: array<non-empty-string, bool>,
     *     expected: non-empty-string,
     * }>
     */
    public static function normalizeWithOptionsProvider(): array
    {
        return [
            [
                'arguments' => '競艇 レース 1',
                'options' => ['shouldRemoveAllSpaces' => true],
                'expected' => '競艇レース1',
            ],
            [
                'arguments' => '競艇 レース 1',
                'options' => ['should_remove_all_spaces' => true],
                'expected' => '競艇レース1',
            ],
            [
                'arguments' => '競艇1レース2',
                'options' => ['shouldRemoveAllNumbers' => true],
                'expected' => '競艇レース',
            ],
            [
                'arguments' => '競艇1レース2',
                'options' => ['shouldRemoveAllNotNumbers' => true],
                'expected' => '12',
            ],
        ];
    }
}
