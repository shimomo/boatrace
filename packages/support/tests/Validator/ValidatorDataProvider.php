<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Validator;

/**
 * @author shimomo
 */
final class ValidatorDataProvider
{
    /**
     * @return non-empty-list<array{
     *     arguments: int,
     *     minimum: int,
     *     maximum: int,
     * }>
     */
    public static function betweenProvider(): array
    {
        return [
            ['arguments' => 1, 'minimum' => 1, 'maximum' => 24],
            ['arguments' => 24, 'minimum' => 1, 'maximum' => 24],
            ['arguments' => 12, 'minimum' => 1, 'maximum' => 12],
        ];
    }

    /**
     * @return non-empty-list<array{
     *     arguments: int,
     *     minimum: int,
     *     maximum: int,
     * }>
     */
    public static function betweenOutOfRangeProvider(): array
    {
        return [
            ['arguments' => 0, 'minimum' => 1, 'maximum' => 24],
            ['arguments' => 25, 'minimum' => 1, 'maximum' => 24],
            ['arguments' => 13, 'minimum' => 1, 'maximum' => 12],
            ['arguments' => -1, 'minimum' => 1, 'maximum' => 12],
        ];
    }
}
