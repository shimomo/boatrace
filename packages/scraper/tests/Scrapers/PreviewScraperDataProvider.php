<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests\Scrapers;

/**
 * @author shimomo
 */
final class PreviewScraperDataProvider
{
    /**
     * @return non-empty-list<array{
     *     key: non-empty-string,
     *     expected: int|float|string|null,
     * }>
     */
    public static function raceProvider(): array
    {
        return [
            ['key' => 'date', 'expected' => '2026-08-01'],
            ['key' => 'stadium_number', 'expected' => 22],
            ['key' => 'race_number', 'expected' => 1],
            ['key' => 'wind_speed_source', 'expected' => '3m'],
            ['key' => 'wind_speed', 'expected' => 3],
            ['key' => 'wind_direction_number_source', 'expected' => '南南東'],
            ['key' => 'wind_direction_number', 'expected' => 8],
            ['key' => 'wave_height_source', 'expected' => '3cm'],
            ['key' => 'wave_height', 'expected' => 3],
            ['key' => 'weather_number_source', 'expected' => '晴'],
            ['key' => 'weather_number', 'expected' => 1],
            ['key' => 'air_temperature', 'expected' => 33.0],
            ['key' => 'water_temperature', 'expected' => 29.0],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     key: non-empty-string,
     *     expected: int|float|string|null,
     * }>
     */
    public static function racerProvider(): array
    {
        return [
            ['key' => 'entry_number', 'expected' => 1],
            ['key' => 'course_number', 'expected' => 1],
            ['key' => 'start_timing_source', 'expected' => '.12'],
            ['key' => 'start_timing', 'expected' => 0.12],
            ['key' => 'weight', 'expected' => 52.0],
            ['key' => 'weight_adjustment', 'expected' => 0.0],
            ['key' => 'exhibition_time', 'expected' => 6.94],
            ['key' => 'tilt_adjustment', 'expected' => -0.5],
            ['key' => 'propeller', 'expected' => null],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     fixture: non-empty-string,
     *     date: non-empty-string,
     *     stadiumNumber: int<1, 24>,
     *     raceNumber: int<1, 12>,
     *     expected: array{
     *         weather_as_of_source: ?string,
     *         weather_as_of_race_number: ?int,
     *         weather_as_of_time: ?string,
     *     },
     * }>
     */
    public static function weatherAsOfProvider(): array
    {
        return [
            [
                'fixture' => 'preview.html',
                'date' => '2026-08-01',
                'stadiumNumber' => 22,
                'raceNumber' => 1,
                'expected' => [
                    'weather_as_of_source' => '18:08現在',
                    'weather_as_of_race_number' => null,
                    'weather_as_of_time' => '18:08',
                ],
            ],
            [
                'fixture' => 'preview-exchange.html',
                'date' => '2026-05-26',
                'stadiumNumber' => 6,
                'raceNumber' => 12,
                'expected' => [
                    'weather_as_of_source' => '11R時点',
                    'weather_as_of_race_number' => 11,
                    'weather_as_of_time' => null,
                ],
            ],
            [
                'fixture' => 'preview-unpublished.html',
                'date' => '2026-08-07',
                'stadiumNumber' => 2,
                'raceNumber' => 1,
                'expected' => [
                    'weather_as_of_source' => null,
                    'weather_as_of_race_number' => null,
                    'weather_as_of_time' => null,
                ],
            ],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     entryNumber: int<1, 6>,
     *     expected: ?string,
     * }>
     */
    public static function propellerProvider(): array
    {
        return [
            ['entryNumber' => 1, 'expected' => null],
            ['entryNumber' => 2, 'expected' => null],
            ['entryNumber' => 3, 'expected' => null],
            ['entryNumber' => 4, 'expected' => '新'],
            ['entryNumber' => 5, 'expected' => null],
            ['entryNumber' => 6, 'expected' => null],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     entryNumber: int<1, 6>,
     *     expected: list<array{
     *         number_source: ?string,
     *         number: ?int,
     *         quantity: ?int,
     *     }>,
     * }>
     */
    public static function partsProvider(): array
    {
        return [
            ['entryNumber' => 1, 'expected' => []],
            ['entryNumber' => 2, 'expected' => [
                ['number_source' => 'ピストン', 'number' => 1, 'quantity' => 2],
                ['number_source' => 'リング', 'number' => 2, 'quantity' => 4],
                ['number_source' => 'シリンダ', 'number' => 5, 'quantity' => null],
            ]],
            ['entryNumber' => 3, 'expected' => []],
            ['entryNumber' => 4, 'expected' => []],
            ['entryNumber' => 5, 'expected' => []],
            ['entryNumber' => 6, 'expected' => []],
        ];
    }
}
