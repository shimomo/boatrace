<?php

declare(strict_types=1);

namespace Boatrace\Support\Validator;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Validator\ValidatorDispatcher as ValidatorDispatcherContract;
use ValueError;

/**
 * @author shimomo
 */
final class ValidatorDispatcher implements ValidatorDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @param ?int $value
     * @param int $minimum
     * @param int $maximum
     * @param non-empty-string $name
     * @return ?int
     * @throws \ValueError
     */
    #[\Override]
    public function between(?int $value, int $minimum, int $maximum, string $name = '$value'): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value < $minimum || $value > $maximum) {
            throw new ValueError(
                sprintf('%s must be between %d and %d, %d given.', $name, $minimum, $maximum, $value)
            );
        }

        return $value;
    }
}
