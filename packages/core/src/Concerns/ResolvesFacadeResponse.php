<?php

declare(strict_types=1);

namespace Boatrace\Core\Concerns;

use Boatrace\Core\CoreContainer;
use LogicException;

/**
 * @author shimomo
 */
trait ResolvesFacadeResponse
{
    /**
     * @template T of object
     * @param class-string<T> $responseContract
     * @param mixed $value
     * @return T
     * @throws \LogicException
     */
    protected static function toResponse(string $responseContract, mixed $value): object
    {
        return self::assertResponse(
            $responseContract,
            CoreContainer::getContainer()->make($responseContract, ['value' => $value]),
        );
    }

    /**
     * @template T of object
     * @param class-string<T> $responseContract
     * @param mixed $response
     * @return T
     * @throws \LogicException
     */
    protected static function assertResponse(string $responseContract, mixed $response): object
    {
        if (!$response instanceof $responseContract) {
            throw new LogicException(
                sprintf('Expected `%s`, got `%s`.', $responseContract, get_debug_type($response))
            );
        }

        return $response;
    }
}
