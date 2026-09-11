<?php

declare(strict_types=1);

namespace Boatrace\Support\Tsv;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Tsv\Tsv as TsvContract;
use Boatrace\Types\Contracts\Tsv\TsvDispatcher as TsvDispatcherContract;
use Boatrace\Types\Contracts\Tsv\TsvResponse as TsvResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Tsv\TsvResponse
 *     parse(?string $value)
 * @author shimomo
 */
final class Tsv implements TsvContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Tsv\TsvDispatcher $tsv
     */
    public function __construct(private readonly TsvDispatcherContract $tsv)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Tsv\TsvResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): TsvResponseContract
    {
        return self::toResponse(TsvResponseContract::class, $this->tsv->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Tsv\TsvResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): TsvResponseContract
    {
        return self::assertResponse(
            TsvResponseContract::class,
            CoreContainer::getInstance(TsvContract::class)->$name(...$arguments),
        );
    }
}
