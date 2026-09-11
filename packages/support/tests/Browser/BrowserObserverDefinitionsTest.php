<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Browser;

use Boatrace\Support\Browser\ObservedHttpBrowser;
use Boatrace\Support\SupportDefinitions;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Browser\BrowserObserver as BrowserObserverContract;
use DI\ContainerBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class BrowserObserverDefinitionsTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function containerBuildsAPlainBrowserByDefault(): void
    {
        $this->assertNotInstanceOf(ObservedHttpBrowser::class, $this->createDispatcher([])->create());
    }

    /**
     * @return void
     */
    #[Test]
    public function containerBuildsAnObservedBrowserWhenTheObserverIsBound(): void
    {
        $dispatcher = $this->createDispatcher([
            BrowserObserverContract::class => \DI\autowire(RecordingBrowserObserver::class),
        ]);

        $this->assertInstanceOf(ObservedHttpBrowser::class, $dispatcher->create());
    }

    /**
     * @param array<class-string, mixed> $definitions
     * @return \Boatrace\Types\Contracts\Browser\BrowserDispatcher
     */
    private function createDispatcher(array $definitions): BrowserDispatcherContract
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(SupportDefinitions::definitions());

        if ($definitions !== []) {
            $containerBuilder->addDefinitions($definitions);
        }

        $dispatcher = $containerBuilder->build()->get(BrowserDispatcherContract::class);

        $this->assertInstanceOf(BrowserDispatcherContract::class, $dispatcher);

        return $dispatcher;
    }
}
