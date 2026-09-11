<?php

declare(strict_types=1);

namespace Boatrace\Support\Timing;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Timing\Timing as TimingContract;
use Boatrace\Types\Contracts\Timing\TimingDispatcher as TimingDispatcherContract;
use Boatrace\Types\Contracts\Timing\TimingResponse as TimingResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Timing\TimingResponse
 *     parse(array|string|null $value)
 * @author shimomo
 */
final class Timing implements TimingContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Timing\TimingDispatcher $timing
     */
    public function __construct(private readonly TimingDispatcherContract $timing)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Timing\TimingResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): TimingResponseContract
    {
        return self::toResponse(TimingResponseContract::class, $this->timing->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Timing\TimingResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): TimingResponseContract
    {
        return self::assertResponse(
            TimingResponseContract::class,
            CoreContainer::getInstance(TimingContract::class)->$name(...$arguments),
        );
    }
}
