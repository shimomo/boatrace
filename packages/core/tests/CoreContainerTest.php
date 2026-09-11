<?php

declare(strict_types=1);

namespace Boatrace\Core\Tests;

use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Core\Core as CoreContract;
use Boatrace\Types\Contracts\Trimmer\Trimmer as TrimmerContract;
use DI\Container;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class CoreContainerTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function getContainerReturnsContainer(): void
    {
        $this->assertInstanceOf(Container::class, CoreContainer::getContainer());
    }

    /**
     * @return void
     */
    #[Test]
    public function getContainerReturnsSameContainer(): void
    {
        $this->assertSame(CoreContainer::getContainer(), CoreContainer::getContainer());
    }

    /**
     * @return void
     */
    #[Test]
    public function getInstanceReturnsSameInstance(): void
    {
        $this->assertSame(
            CoreContainer::getInstance(CoreContract::class),
            CoreContainer::getInstance(CoreContract::class)
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function getContainerResolvesDefinitionsOfOtherPackages(): void
    {
        $this->assertInstanceOf(
            TrimmerContract::class,
            CoreContainer::getInstance(TrimmerContract::class)
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function addDefinitionsAppendsDefinitions(): void
    {
        CoreContainer::addDefinitions(__DIR__ . '/fixtures/definitions.php');

        $this->assertSame('競艇', CoreContainer::getContainer()->get('boatrace.tests.value'));
        $this->assertInstanceOf(CoreContract::class, CoreContainer::getInstance(CoreContract::class));
    }
}
