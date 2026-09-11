<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Tsv;

use BadMethodCallException;
use Boatrace\Support\Tsv\TsvDispatcher;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class TsvDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Tsv\TsvDispatcher
     */
    protected TsvDispatcher $tsv;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->tsv = new TsvDispatcher();
    }

    /**
     * @param string $arguments
     * @param list<list<string>> $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TsvDataProvider::class, 'parseProvider')]
    public function parseReturnsRows(string $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->tsv->parse($arguments));
    }

    /**
     * @return void
     */
    #[Test]
    public function parseReturnsEmptyArrayWhenGivenNull(): void
    {
        $this->assertSame([], $this->tsv->parse(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Tsv\TsvDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->tsv->ghost();
    }
}
