<?php

declare(strict_types=1);

namespace Boatrace\Types\Tests\Enums;

/**
 * @author shimomo
 */
final class StadiumDataProvider
{
    /**
     * @return non-empty-list<array{
     *     arguments: non-empty-string,
     *     expected: int<1, 24>,
     * }>
     */
    public static function fromNameProvider(): array
    {
        return [
            ['arguments' => '桐生', 'expected' => 1],
            ['arguments' => 'びわこ', 'expected' => 11],
            ['arguments' => '福岡', 'expected' => 22],
            ['arguments' => '大村', 'expected' => 24],
        ];
    }
}
