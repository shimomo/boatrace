<?php

declare(strict_types=1);

namespace Boatrace\Support\Browser;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Browser\Browser as BrowserContract;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Browser\BrowserResponse as BrowserResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Browser\BrowserResponse
 *     create(array $serverParameters = [])
 * @author shimomo
 */
final class Browser implements BrowserContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Browser\BrowserDispatcher $browser
     */
    public function __construct(private readonly BrowserDispatcherContract $browser)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Browser\BrowserResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): BrowserResponseContract
    {
        return self::toResponse(BrowserResponseContract::class, $this->browser->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Browser\BrowserResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): BrowserResponseContract
    {
        return self::assertResponse(
            BrowserResponseContract::class,
            CoreContainer::getInstance(BrowserContract::class)->$name(...$arguments),
        );
    }
}
