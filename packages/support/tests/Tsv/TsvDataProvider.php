<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Tsv;

/**
 * @author shimomo
 */
final class TsvDataProvider
{
    /**
     * @return non-empty-list<array{
     *     arguments: string,
     *     expected: list<list<string>>,
     * }>
     */
    public static function parseProvider(): array
    {
        return [
            ['arguments' => '', 'expected' => []],
            ['arguments' => '競艇', 'expected' => [['競艇']]],
            ['arguments' => "1\t2\t3", 'expected' => [['1', '2', '3']]],
            ['arguments' => "1\t2\n3\t4", 'expected' => [['1', '2'], ['3', '4']]],
            ['arguments' => "1\t2\r\n3\t4\r\n", 'expected' => [['1', '2'], ['3', '4']]],
            ['arguments' => "1\t2\n\n\n", 'expected' => [['1', '2']]],
            ['arguments' => "1\t\t3", 'expected' => [['1', '', '3']]],
            ['arguments' => "1\t2\t\n", 'expected' => [['1', '2', '']]],
            ['arguments' => "1\t2\n\n3\t4\n", 'expected' => [['1', '2'], [''], ['3', '4']]],
        ];
    }
}
