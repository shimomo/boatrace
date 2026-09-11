<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Trimmer;

/**
 * @author shimomo
 */
final class TrimmerDataProvider
{
    /**
     * @return non-empty-list<array{
     *     arguments: non-empty-list<string>,
     *     expected: non-empty-string,
     * }>
     */
    public static function trimOrNullProvider(): array
    {
        return [
            ['arguments' => [' 競艇 '], 'expected' => '競艇'],
            ['arguments' => [' 競艇'], 'expected' => '競艇'],
            ['arguments' => ['競艇 '], 'expected' => '競艇'],
            ['arguments' => ['競艇'], 'expected' => '競艇'],
            ['arguments' => ['競艇', '競'], 'expected' => '艇'],
            ['arguments' => ['競艇', '艇'], 'expected' => '競'],
            ['arguments' => [' 競艇 ', '競艇'], 'expected' => ' 競艇 '],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     arguments: non-empty-list<string>,
     *     expected: non-empty-string,
     * }>
     */
    public static function ltrimOrNullProvider(): array
    {
        return [
            ['arguments' => [' 競艇 '], 'expected' => '競艇 '],
            ['arguments' => [' 競艇'], 'expected' => '競艇'],
            ['arguments' => ['競艇 '], 'expected' => '競艇 '],
            ['arguments' => ['競艇'], 'expected' => '競艇'],
            ['arguments' => ['競艇', '競'], 'expected' => '艇'],
            ['arguments' => ['競艇', '艇'], 'expected' => '競艇'],
            ['arguments' => [' 競艇 ', '競艇'], 'expected' => ' 競艇 '],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     arguments: non-empty-list<string>,
     *     expected: non-empty-string,
     * }>
     */
    public static function rtrimOrNullProvider(): array
    {
        return [
            ['arguments' => [' 競艇 '], 'expected' => ' 競艇'],
            ['arguments' => [' 競艇'], 'expected' => ' 競艇'],
            ['arguments' => ['競艇 '], 'expected' => '競艇'],
            ['arguments' => ['競艇'], 'expected' => '競艇'],
            ['arguments' => ['競艇', '競'], 'expected' => '競艇'],
            ['arguments' => ['競艇', '艇'], 'expected' => '競'],
            ['arguments' => [' 競艇 ', '競艇'], 'expected' => ' 競艇 '],
        ];
    }
}
