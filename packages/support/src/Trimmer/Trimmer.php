<?php

declare(strict_types=1);

namespace Boatrace\Support\Trimmer;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Trimmer\Trimmer as TrimmerContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher as TrimmerDispatcherContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerResponse as TrimmerResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Trimmer\TrimmerResponse
 *     trimOrNull(?string $value, ?string $characters = null)
 * @method static \Boatrace\Types\Contracts\Trimmer\TrimmerResponse
 *     ltrimOrNull(?string $value, ?string $characters = null)
 * @method static \Boatrace\Types\Contracts\Trimmer\TrimmerResponse
 *     rtrimOrNull(?string $value, ?string $characters = null)
 * @author shimomo
 */
final class Trimmer implements TrimmerContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher $trimmer
     */
    public function __construct(private readonly TrimmerDispatcherContract $trimmer)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Trimmer\TrimmerResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): TrimmerResponseContract
    {
        return self::toResponse(TrimmerResponseContract::class, $this->trimmer->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Trimmer\TrimmerResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): TrimmerResponseContract
    {
        return self::assertResponse(
            TrimmerResponseContract::class,
            CoreContainer::getInstance(TrimmerContract::class)->$name(...$arguments),
        );
    }
}
