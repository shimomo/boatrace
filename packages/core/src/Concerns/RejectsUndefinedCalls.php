<?php

declare(strict_types=1);

namespace Boatrace\Core\Concerns;

use BadMethodCallException;

/**
 * @author shimomo
 */
trait RejectsUndefinedCalls
{
    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return never
     * @throws \BadMethodCallException
     */
    public function __call(string $name, array $arguments): never
    {
        throw new BadMethodCallException(
            'Call to undefined method `' . static::class . '::' . $name . '()`.'
        );
    }
}
