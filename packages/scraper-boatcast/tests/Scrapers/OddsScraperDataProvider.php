<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests\Scrapers;

/**
 * @author shimomo
 */
final class OddsScraperDataProvider
{
    /**
     * @return non-empty-list<array{
     *     bettingMethod: non-empty-string,
     *     combination: non-empty-list<int<1, 6>>,
     *     expected: float,
     * }>
     */
    public static function oddsProvider(): array
    {
        return [
            ['bettingMethod' => 'trifecta', 'combination' => [1, 2, 3], 'expected' => 6.9],
            ['bettingMethod' => 'trifecta', 'combination' => [1, 2, 6], 'expected' => 60.8],
            ['bettingMethod' => 'trifecta', 'combination' => [6, 5, 4], 'expected' => 1912.0],
            ['bettingMethod' => 'trio', 'combination' => [1, 2, 3], 'expected' => 3.8],
            ['bettingMethod' => 'trio', 'combination' => [4, 5, 6], 'expected' => 83.5],
            ['bettingMethod' => 'exacta', 'combination' => [1, 2], 'expected' => 2.3],
            ['bettingMethod' => 'exacta', 'combination' => [1, 6], 'expected' => 70.5],
            ['bettingMethod' => 'exacta', 'combination' => [6, 1], 'expected' => 155.1],
            ['bettingMethod' => 'quinella', 'combination' => [1, 2], 'expected' => 1.7],
            ['bettingMethod' => 'quinella', 'combination' => [5, 6], 'expected' => 118.5],
            ['bettingMethod' => 'win', 'combination' => [1], 'expected' => 1.1],
            ['bettingMethod' => 'win', 'combination' => [6], 'expected' => 35.2],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     bettingMethod: non-empty-string,
     *     combination: non-empty-list<int<1, 6>>,
     *     lowerLimit: float,
     *     upperLimit: float,
     * }>
     */
    public static function rangedOddsProvider(): array
    {
        return [
            [
                'bettingMethod' => 'quinella_place',
                'combination' => [1, 2],
                'lowerLimit' => 1.7,
                'upperLimit' => 2.4,
            ],
            [
                'bettingMethod' => 'quinella_place',
                'combination' => [5, 6],
                'lowerLimit' => 14.1,
                'upperLimit' => 18.0,
            ],
            [
                'bettingMethod' => 'place',
                'combination' => [1],
                'lowerLimit' => 1.0,
                'upperLimit' => 1.0,
            ],
            [
                'bettingMethod' => 'place',
                'combination' => [6],
                'lowerLimit' => 2.3,
                'upperLimit' => 4.6,
            ],
        ];
    }
}
