<?php

declare(strict_types=1);

namespace Boatrace\Support\Json;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Json\Json as JsonContract;
use Boatrace\Types\Contracts\Json\JsonDispatcher as JsonDispatcherContract;
use Boatrace\Types\Contracts\Json\JsonResponse as JsonResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Json\JsonResponse
 *     decode(?string $value)
 * @author shimomo
 */
final class Json implements JsonContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Json\JsonDispatcher $json
     */
    public function __construct(private readonly JsonDispatcherContract $json)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Json\JsonResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): JsonResponseContract
    {
        return self::toResponse(JsonResponseContract::class, $this->json->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Json\JsonResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): JsonResponseContract
    {
        return self::assertResponse(
            JsonResponseContract::class,
            CoreContainer::getInstance(JsonContract::class)->$name(...$arguments),
        );
    }
}
