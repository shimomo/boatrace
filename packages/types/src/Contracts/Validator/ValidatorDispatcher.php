<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Validator;

/**
 * @author shimomo
 */
interface ValidatorDispatcher extends Validator
{
    /**
     * @param ?int $value
     * @param int $minimum
     * @param int $maximum
     * @param non-empty-string $name
     * @return ?int
     * @throws \ValueError
     */
    public function between(?int $value, int $minimum, int $maximum, string $name = '$value'): ?int;
}
