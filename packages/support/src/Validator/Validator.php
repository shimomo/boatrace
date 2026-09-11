<?php

declare(strict_types=1);

namespace Boatrace\Support\Validator;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Validator\Validator as ValidatorContract;
use Boatrace\Types\Contracts\Validator\ValidatorDispatcher as ValidatorDispatcherContract;
use Boatrace\Types\Contracts\Validator\ValidatorResponse as ValidatorResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Validator\ValidatorResponse
 *     between(?int $value, int $minimum, int $maximum, string $name = '$value')
 * @author shimomo
 */
final class Validator implements ValidatorContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Validator\ValidatorDispatcher $validator
     */
    public function __construct(private readonly ValidatorDispatcherContract $validator)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Validator\ValidatorResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): ValidatorResponseContract
    {
        return self::toResponse(ValidatorResponseContract::class, $this->validator->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Validator\ValidatorResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): ValidatorResponseContract
    {
        return self::assertResponse(
            ValidatorResponseContract::class,
            CoreContainer::getInstance(ValidatorContract::class)->$name(...$arguments),
        );
    }
}
