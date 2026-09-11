<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Timing;

use BadMethodCallException;
use Boatrace\Support\Timing\TimingDispatcher;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class TimingDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Timing\TimingDispatcher
     */
    protected TimingDispatcher $timing;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->timing = new TimingDispatcher();
    }

    /**
     * @param list<string>|string $arguments
     * @param array<non-empty-string, array{dur: ?float, desc: ?string}> $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TimingDataProvider::class, 'parseProvider')]
    public function parseReturnsMetrics(array|string $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->timing->parse($arguments));
    }

    /**
     * @return void
     */
    #[Test]
    public function parseReturnsEmptyArrayWhenGivenNull(): void
    {
        $this->assertSame([], $this->timing->parse(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Timing\TimingDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->timing->ghost();
    }
}
