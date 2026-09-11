<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests\Scrapers;

/**
 * @author shimomo
 */
final class TimeScraperDataProvider
{
    /**
     * @return non-empty-list<array{
     *     fixture: non-empty-string,
     *     stadiumNumber: int<1, 24>,
     *     expected: array<non-empty-string, int|float|string|null>,
     * }>
     */
    public static function scrapeProvider(): array
    {
        return [
            [
                'fixture' => 'oriten-ashiya.txt',
                'stadiumNumber' => 21,
                'expected' => [
                    'entry_number' => 1,
                    'name' => '井上 恵一',
                    'lap_time' => 36.88,
                    'half_lap_time' => null,
                    'turn_time' => 7.73,
                    'straight_time' => 7.78,
                ],
            ],
            [
                'fixture' => 'oriten-kiryu.txt',
                'stadiumNumber' => 1,
                'expected' => [
                    'entry_number' => 1,
                    'name' => '柴田 愛梨',
                    'lap_time' => null,
                    'half_lap_time' => 18.50,
                    'turn_time' => 4.30,
                    'straight_time' => 7.43,
                ],
            ],
            [
                'fixture' => 'oriten-suminoe.txt',
                'stadiumNumber' => 12,
                'expected' => [
                    'entry_number' => 1,
                    'name' => '佐藤 大騎',
                    'lap_time' => 37.48,
                    'half_lap_time' => null,
                    'turn_time' => 11.49,
                    'straight_time' => null,
                ],
            ],
        ];
    }
}
