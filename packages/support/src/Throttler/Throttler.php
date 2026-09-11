<?php

declare(strict_types=1);

namespace Boatrace\Support\Throttler;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Throttler\Throttler as ThrottlerContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerResponse as ThrottlerResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Throttler\ThrottlerResponse
 *     getMinCallIntervalSeconds()
 * @method static \Boatrace\Types\Contracts\Throttler\ThrottlerResponse
 *     setMinCallIntervalSeconds(float $seconds)
 * @method static \Boatrace\Types\Contracts\Throttler\ThrottlerResponse
 *     throttle()
 * @author shimomo
 */
final class Throttler implements ThrottlerContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher $throttler
     */
    public function __construct(private readonly ThrottlerDispatcherContract $throttler)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Throttler\ThrottlerResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): ThrottlerResponseContract
    {
        return self::toResponse(ThrottlerResponseContract::class, $this->throttler->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Throttler\ThrottlerResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): ThrottlerResponseContract
    {
        return self::assertResponse(
            ThrottlerResponseContract::class,
            CoreContainer::getInstance(ThrottlerContract::class)->$name(...$arguments),
        );
    }
}
