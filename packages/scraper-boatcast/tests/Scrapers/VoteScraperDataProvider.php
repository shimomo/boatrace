<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests\Scrapers;

/**
 * @author shimomo
 */
final class VoteScraperDataProvider
{
    /**
     * @return non-empty-list<array{
     *     bettingMethod: non-empty-string,
     *     combination: non-empty-list<int<1, 6>>,
     *     expected: int,
     * }>
     */
    public static function voteProvider(): array
    {
        return [
            ['bettingMethod' => 'trifecta', 'combination' => [1, 2, 3], 'expected' => 19241],
            ['bettingMethod' => 'trifecta', 'combination' => [6, 5, 4], 'expected' => 70],
            ['bettingMethod' => 'trio', 'combination' => [1, 2, 3], 'expected' => 429],
            ['bettingMethod' => 'trio', 'combination' => [4, 5, 6], 'expected' => 20],
            ['bettingMethod' => 'exacta', 'combination' => [1, 2], 'expected' => 1333],
            ['bettingMethod' => 'quinella', 'combination' => [1, 2], 'expected' => 206],
            ['bettingMethod' => 'quinella_place', 'combination' => [1, 2], 'expected' => 35],
            ['bettingMethod' => 'win', 'combination' => [1], 'expected' => 127],
            ['bettingMethod' => 'place', 'combination' => [1], 'expected' => 48],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     bettingMethod: non-empty-string,
     *     expected: int,
     * }>
     */
    public static function totalProvider(): array
    {
        return [
            ['bettingMethod' => 'trifecta', 'expected' => 178529],
            ['bettingMethod' => 'trio', 'expected' => 2227],
            ['bettingMethod' => 'exacta', 'expected' => 4138],
            ['bettingMethod' => 'quinella', 'expected' => 474],
            ['bettingMethod' => 'quinella_place', 'expected' => 284],
            ['bettingMethod' => 'win', 'expected' => 188],
            ['bettingMethod' => 'place', 'expected' => 84],
        ];
    }
}
