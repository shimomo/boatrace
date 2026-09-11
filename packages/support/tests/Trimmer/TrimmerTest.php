<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Trimmer;

use BadMethodCallException;
use Boatrace\Support\Trimmer\Trimmer;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class TrimmerTest extends TestCase
{
    /**
     * @param non-empty-list<string> $arguments
     * @param non-empty-string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TrimmerDataProvider::class, 'trimOrNullProvider')]
    public function trimOrNullReturnsTrimmedString(array $arguments, string $expected): void
    {
        $this->assertSame($expected, Trimmer::trimOrNull(...$arguments)->getValue());
    }

    /**
     * @param non-empty-list<string> $arguments
     * @param non-empty-string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TrimmerDataProvider::class, 'ltrimOrNullProvider')]
    public function ltrimOrNullReturnsTrimmedString(array $arguments, string $expected): void
    {
        $this->assertSame($expected, Trimmer::ltrimOrNull(...$arguments)->getValue());
    }

    /**
     * @param non-empty-list<string> $arguments
     * @param non-empty-string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TrimmerDataProvider::class, 'rtrimOrNullProvider')]
    public function rtrimOrNullReturnsTrimmedString(array $arguments, string $expected): void
    {
        $this->assertSame($expected, Trimmer::rtrimOrNull(...$arguments)->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function trimOrNullReturnsNullWhenGivenNull(): void
    {
        $this->assertNull(Trimmer::trimOrNull(null)->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function ltrimOrNullReturnsNullWhenGivenNull(): void
    {
        $this->assertNull(Trimmer::ltrimOrNull(null)->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function rtrimOrNullReturnsNullWhenGivenNull(): void
    {
        $this->assertNull(Trimmer::rtrimOrNull(null)->getValue());
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
        Trimmer::ghost();
    }
}
