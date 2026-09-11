<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Throttler;

use BadMethodCallException;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Throttler\ThrottlerDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @author shimomo
 */
final class ThrottlerDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Throttler\ThrottlerDispatcher
     */
    protected ThrottlerDispatcher $throttler;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->throttler = new ThrottlerDispatcher(
            new ConverterDispatcher(new TrimmerDispatcher())
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function getMinCallIntervalSecondsReturnsDefaultSeconds(): void
    {
        $this->assertSame(1.0, $this->throttler->getMinCallIntervalSeconds());
    }

    /**
     * @return void
     */
    #[Test]
    public function setMinCallIntervalSecondsUpdatesSeconds(): void
    {
        $this->throttler->setMinCallIntervalSeconds(5.0);

        $this->assertSame(5.0, $this->throttler->getMinCallIntervalSeconds());
    }

    /**
     * @return void
     */
    #[Test]
    public function setMinCallIntervalSecondsThrowsValueErrorWhenGivenNegativeSeconds(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$seconds must be 0.0 or greater, -0.5 given.');

        $this->throttler->setMinCallIntervalSeconds(-0.5);
    }

    /**
     * @return void
     */
    #[Test]
    public function setMinCallIntervalSecondsAcceptsZero(): void
    {
        $this->throttler->setMinCallIntervalSeconds(0.0);

        $this->assertSame(0.0, $this->throttler->getMinCallIntervalSeconds());
    }

    /**
     * @return void
     */
    #[Test]
    public function constructorThrowsValueErrorWhenGivenNegativeSeconds(): void
    {
        $this->expectException(ValueError::class);

        new ThrottlerDispatcher(new ConverterDispatcher(new TrimmerDispatcher()), -1.0);
    }

    /**
     * @return void
     */
    #[Test]
    public function throttleReturnsImmediatelyOnFirstCall(): void
    {
        $startedAt = microtime(true);

        $this->throttler->throttle();

        $this->assertLessThan(0.5, microtime(true) - $startedAt);
    }

    /**
     * @return void
     */
    #[Test]
    public function throttleWaitsForMinCallIntervalSeconds(): void
    {
        $throttler = new ThrottlerDispatcher(
            new ConverterDispatcher(new TrimmerDispatcher()),
            0.2,
            false
        );

        $throttler->throttle();

        $startedAt = microtime(true);
        $throttler->throttle();
        $elapsedSeconds = microtime(true) - $startedAt;

        $this->assertGreaterThanOrEqual(0.15, $elapsedSeconds);
        $this->assertLessThan(1.0, $elapsedSeconds);
    }

    /**
     * @return void
     */
    #[Test]
    public function throttleVariesTheIntervalWhenJitterIsEnabled(): void
    {
        $throttler = new ThrottlerDispatcher(
            new ConverterDispatcher(new TrimmerDispatcher()),
            0.1
        );

        $throttler->throttle();

        $elapsed = [];

        foreach (range(1, 6) as $ignored) {
            $startedAt = microtime(true);
            $throttler->throttle();
            $elapsed[] = microtime(true) - $startedAt;
        }

        $this->assertGreaterThan(0.005, max($elapsed) - min($elapsed));

        $this->assertGreaterThanOrEqual(0.04, min($elapsed));
        $this->assertLessThan(0.3, max($elapsed));
    }

    /**
     * @return void
     */
    #[Test]
    public function throttleDoesNotWaitWhenIntervalIsZero(): void
    {
        $throttler = new ThrottlerDispatcher(new ConverterDispatcher(new TrimmerDispatcher()), 0.0);

        $throttler->throttle();

        $startedAt = microtime(true);
        $throttler->throttle();

        $this->assertLessThan(0.05, microtime(true) - $startedAt);
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Throttler\ThrottlerDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->throttler->ghost();
    }
}
