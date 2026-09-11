<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Trimmer;

use BadMethodCallException;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class TrimmerDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Trimmer\TrimmerDispatcher
     */
    protected TrimmerDispatcher $trimmer;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->trimmer = new TrimmerDispatcher();
    }

    /**
     * @param non-empty-list<string> $arguments
     * @param string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TrimmerDataProvider::class, 'trimOrNullProvider')]
    public function trimOrNullReturnsTrimmedString(array $arguments, string $expected): void
    {
        $this->assertSame($expected, $this->trimmer->trimOrNull(...$arguments));
    }

    /**
     * @param non-empty-list<string> $arguments
     * @param string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TrimmerDataProvider::class, 'ltrimOrNullProvider')]
    public function ltrimOrNullReturnsTrimmedString(array $arguments, string $expected): void
    {
        $this->assertSame($expected, $this->trimmer->ltrimOrNull(...$arguments));
    }

    /**
     * @param non-empty-list<string> $arguments
     * @param string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TrimmerDataProvider::class, 'rtrimOrNullProvider')]
    public function rtrimOrNullReturnsTrimmedString(array $arguments, string $expected): void
    {
        $this->assertSame($expected, $this->trimmer->rtrimOrNull(...$arguments));
    }

    /**
     * @return void
     */
    #[Test]
    public function trimOrNullReturnsNullWhenGivenNull(): void
    {
        $this->assertNull($this->trimmer->trimOrNull(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function ltrimOrNullReturnsNullWhenGivenNull(): void
    {
        $this->assertNull($this->trimmer->ltrimOrNull(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function rtrimOrNullReturnsNullWhenGivenNull(): void
    {
        $this->assertNull($this->trimmer->rtrimOrNull(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Trimmer\TrimmerDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->trimmer->ghost();
    }
}
