<?php

declare(strict_types=1);

namespace Boatrace\Support\Progress;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Progress\Progress as ProgressContract;
use Boatrace\Types\Contracts\Progress\ProgressDispatcher as ProgressDispatcherContract;
use Boatrace\Types\Contracts\Progress\ProgressResponse as ProgressResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Progress\ProgressResponse
 *     isEnabled()
 * @method static \Boatrace\Types\Contracts\Progress\ProgressResponse
 *     setEnabled(bool $enabled)
 * @method static \Boatrace\Types\Contracts\Progress\ProgressResponse
 *     start(int $totalSteps, ?string $message = null)
 * @method static \Boatrace\Types\Contracts\Progress\ProgressResponse
 *     advance(int $step = 1)
 * @method static \Boatrace\Types\Contracts\Progress\ProgressResponse
 *     finish(?string $message = null)
 * @author shimomo
 */
final class Progress implements ProgressContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Progress\ProgressDispatcher $progress
     */
    public function __construct(private readonly ProgressDispatcherContract $progress)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Progress\ProgressResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): ProgressResponseContract
    {
        return self::toResponse(ProgressResponseContract::class, $this->progress->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Progress\ProgressResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): ProgressResponseContract
    {
        return self::assertResponse(
            ProgressResponseContract::class,
            CoreContainer::getInstance(ProgressContract::class)->$name(...$arguments),
        );
    }
}
