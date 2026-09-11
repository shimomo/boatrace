<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Converter;

/**
 * @author shimomo
 */
final class ConverterDataProvider
{
    /**
     * @return non-empty-list<array{
     *     arguments: int|float|string,
     *     expected: int,
     * }>
     */
    public static function toIntProvider(): array
    {
        return [
            ['arguments' => 1, 'expected' => 1],
            ['arguments' => 1.9, 'expected' => 1],
            ['arguments' => '1', 'expected' => 1],
            ['arguments' => '1.9', 'expected' => 1],
            ['arguments' => '競艇', 'expected' => 0],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     arguments: int|float|string,
     *     expected: float,
     * }>
     */
    public static function toFloatProvider(): array
    {
        return [
            ['arguments' => 1, 'expected' => 1.0],
            ['arguments' => 1.9, 'expected' => 1.9],
            ['arguments' => '1', 'expected' => 1.0],
            ['arguments' => '1.9', 'expected' => 1.9],
            ['arguments' => '競艇', 'expected' => 0.0],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     arguments: int|float|string,
     *     expected: int|float|string,
     * }>
     */
    public static function toIntOrReturnProvider(): array
    {
        return [
            ['arguments' => '1', 'expected' => 1],
            ['arguments' => '1.9', 'expected' => 1],
            ['arguments' => '競艇', 'expected' => '競艇'],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     arguments: non-empty-string,
     *     expected: non-empty-string,
     * }>
     */
    public static function toKanaProvider(): array
    {
        return [
            ['arguments' => '１２３', 'expected' => '123'],
            ['arguments' => 'ｱｲｳ', 'expected' => 'アイウ'],
            ['arguments' => 'ＢＯＡＴ', 'expected' => 'BOAT'],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     arguments: non-empty-string,
     *     expected: ?int,
     * }>
     */
    public static function toDayNumberProvider(): array
    {
        return [
            ['arguments' => ' １日目 ', 'expected' => 1],
            ['arguments' => '２日目', 'expected' => 2],
            ['arguments' => '6日目', 'expected' => 6],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     arguments: non-empty-string,
     *     expected: non-empty-string,
     * }>
     */
    public static function toCamelCaseProvider(): array
    {
        return [
            ['arguments' => 'stadium_number', 'expected' => 'stadiumNumber'],
            ['arguments' => 'should_remove_all_spaces', 'expected' => 'shouldRemoveAllSpaces'],
            ['arguments' => 'number', 'expected' => 'number'],
        ];
    }
}
