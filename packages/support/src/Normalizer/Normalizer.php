<?php

declare(strict_types=1);

namespace Boatrace\Support\Normalizer;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Normalizer\Normalizer as NormalizerContract;
use Boatrace\Types\Contracts\Normalizer\NormalizerDispatcher as NormalizerDispatcherContract;
use Boatrace\Types\Contracts\Normalizer\NormalizerResponse as NormalizerResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Normalizer\NormalizerResponse
 *     normalize(int|float|string|array|null $value, array $options = [])
 * @author shimomo
 */
final class Normalizer implements NormalizerContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Normalizer\NormalizerDispatcher $normalizer
     */
    public function __construct(private readonly NormalizerDispatcherContract $normalizer)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Normalizer\NormalizerResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): NormalizerResponseContract
    {
        return self::toResponse(NormalizerResponseContract::class, $this->normalizer->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Normalizer\NormalizerResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): NormalizerResponseContract
    {
        return self::assertResponse(
            NormalizerResponseContract::class,
            CoreContainer::getInstance(NormalizerContract::class)->$name(...$arguments),
        );
    }
}
