<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Timing;

/**
 * @author shimomo
 */
final class TimingDataProvider
{
    /**
     * @return non-empty-list<array{
     *     arguments: list<string>|string,
     *     expected: array<non-empty-string, array{dur: ?float, desc: ?string}>,
     * }>
     */
    public static function parseProvider(): array
    {
        return [
            ['arguments' => '', 'expected' => []],
            ['arguments' => [], 'expected' => []],
            [
                'arguments' => 'edge; dur=157',
                'expected' => ['edge' => ['dur' => 157.0, 'desc' => null]],
            ],
            [
                'arguments' => 'cdn-cache; desc=MISS',
                'expected' => ['cdn-cache' => ['dur' => null, 'desc' => 'MISS']],
            ],
            [
                'arguments' => [
                    'cdn-cache; desc=MISS',
                    'edge; dur=157',
                    'origin; dur=185',
                    'ak_p; desc="1786110627228_389047462_898320635_34162_18110_37_402_15";dur=1',
                ],
                'expected' => [
                    'cdn-cache' => ['dur' => null, 'desc' => 'MISS'],
                    'edge' => ['dur' => 157.0, 'desc' => null],
                    'origin' => ['dur' => 185.0, 'desc' => null],
                    'ak_p' => [
                        'dur' => 1.0,
                        'desc' => '1786110627228_389047462_898320635_34162_18110_37_402_15',
                    ],
                ],
            ],
            [
                'arguments' => ['edge; dur=8012', 'origin; dur=23'],
                'expected' => [
                    'edge' => ['dur' => 8012.0, 'desc' => null],
                    'origin' => ['dur' => 23.0, 'desc' => null],
                ],
            ],
            [
                'arguments' => 'cdn-cache; desc=HIT, edge; dur=12, origin; dur=0',
                'expected' => [
                    'cdn-cache' => ['dur' => null, 'desc' => 'HIT'],
                    'edge' => ['dur' => 12.0, 'desc' => null],
                    'origin' => ['dur' => 0.0, 'desc' => null],
                ],
            ],
            [
                'arguments' => 'miss; desc="a, b; c", edge; dur=1',
                'expected' => [
                    'miss' => ['dur' => null, 'desc' => 'a, b; c'],
                    'edge' => ['dur' => 1.0, 'desc' => null],
                ],
            ],
            [
                'arguments' => 'cache; desc="say \"hi\""',
                'expected' => ['cache' => ['dur' => null, 'desc' => 'say "hi"']],
            ],
            [
                'arguments' => 'app;dur=23.5;desc=controller',
                'expected' => ['app' => ['dur' => 23.5, 'desc' => 'controller']],
            ],
            [
                'arguments' => '  edge ;  dur = 42  ',
                'expected' => ['edge' => ['dur' => 42.0, 'desc' => null]],
            ],
            ['arguments' => 'edge; dur=', 'expected' => ['edge' => ['dur' => null, 'desc' => null]]],
            ['arguments' => 'edge; dur=fast', 'expected' => ['edge' => ['dur' => null, 'desc' => null]]],
            ['arguments' => 'edge; dur', 'expected' => ['edge' => ['dur' => null, 'desc' => null]]],
            ['arguments' => 'edge', 'expected' => ['edge' => ['dur' => null, 'desc' => null]]],
            ['arguments' => ';;;', 'expected' => []],
            ['arguments' => ',,', 'expected' => []],
            ['arguments' => ['', 'edge; dur=1'], 'expected' => ['edge' => ['dur' => 1.0, 'desc' => null]]],
            [
                'arguments' => ['edge; dur=1', 'edge; dur=2'],
                'expected' => ['edge' => ['dur' => 2.0, 'desc' => null]],
            ],
        ];
    }
}
