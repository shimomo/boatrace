<?php

declare(strict_types=1);

namespace Boatrace\Core;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Types\Contracts\Core\Core as CoreContract;
use Boatrace\Types\Contracts\Core\CoreDispatcher as CoreDispatcherContract;
use Boatrace\Types\Contracts\Core\CoreResponse as CoreResponseContract;

/**
 * @author shimomo
 */
final class Core implements CoreContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Core\CoreDispatcher $core
     */
    public function __construct(private readonly CoreDispatcherContract $core)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Core\CoreResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): CoreResponseContract
    {
        return self::toResponse(CoreResponseContract::class, $this->core->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Core\CoreResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): CoreResponseContract
    {
        return self::assertResponse(
            CoreResponseContract::class,
            CoreContainer::getInstance(CoreContract::class)->$name(...$arguments),
        );
    }
}
