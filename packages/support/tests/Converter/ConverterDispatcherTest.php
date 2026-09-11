<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Converter;

use BadMethodCallException;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use Boatrace\Types\Enums\Stadium;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ConverterDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Converter\ConverterDispatcher
     */
    protected ConverterDispatcher $converter;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->converter = new ConverterDispatcher(new TrimmerDispatcher());
    }

    /**
     * @param int|float|string $arguments
     * @param int $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(ConverterDataProvider::class, 'toIntProvider')]
    public function toIntReturnsInt(int|float|string $arguments, int $expected): void
    {
        $this->assertSame($expected, $this->converter->toInt($arguments));
    }

    /**
     * @param int|float|string $arguments
     * @param float $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(ConverterDataProvider::class, 'toFloatProvider')]
    public function toFloatReturnsFloat(int|float|string $arguments, float $expected): void
    {
        $this->assertSame($expected, $this->converter->toFloat($arguments));
    }

    /**
     * @param int|float|string $arguments
     * @param int|float|string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(ConverterDataProvider::class, 'toIntOrReturnProvider')]
    public function toIntOrReturnReturnsIntOrValue(
        int|float|string $arguments,
        int|float|string $expected
    ): void {
        $this->assertSame($expected, $this->converter->toIntOrReturn($arguments));
    }

    /**
     * @param non-empty-string $arguments
     * @param non-empty-string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(ConverterDataProvider::class, 'toKanaProvider')]
    public function toKanaReturnsConvertedString(string $arguments, string $expected): void
    {
        $this->assertSame($expected, $this->converter->toKana($arguments));
    }

    /**
     * @param non-empty-string $arguments
     * @param ?int $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(ConverterDataProvider::class, 'toDayNumberProvider')]
    public function toDayNumberReturnsDayNumber(string $arguments, ?int $expected): void
    {
        $this->assertSame($expected, $this->converter->toDayNumber($arguments));
    }

    /**
     * @param non-empty-string $arguments
     * @param non-empty-string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(ConverterDataProvider::class, 'toCamelCaseProvider')]
    public function toCamelCaseReturnsCamelCasedString(string $arguments, string $expected): void
    {
        $this->assertSame($expected, $this->converter->toCamelCase($arguments));
    }

    /**
     * @return void
     */
    #[Test]
    public function toCamelCaseKeysReturnsCamelCasedKeys(): void
    {
        $this->assertSame(
            ['stadiumNumber' => 1, 'raceNumber' => 2],
            $this->converter->toCamelCaseKeys(['stadium_number' => 1, 'race_number' => 2])
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function toNullReturnsNull(): void
    {
        $this->assertNull($this->converter->toNull('競艇'));
    }

    /**
     * @return void
     */
    #[Test]
    public function convertersReturnNullWhenGivenNull(): void
    {
        $this->assertNull($this->converter->toInt(null));
        $this->assertNull($this->converter->toFloat(null));
        $this->assertNull($this->converter->toString(null));
        $this->assertNull($this->converter->toKana(null));
        $this->assertNull($this->converter->toDayNumber(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function toEnumOrNullReturnsEnum(): void
    {
        $this->assertSame(
            Stadium::福岡,
            $this->converter->toEnumOrNull(fn(): ?Stadium => Stadium::fromName('福岡'))
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function toEnumOrNullReturnsNullWhenResolverThrowsValueError(): void
    {
        $this->assertNull(
            $this->converter->toEnumOrNull(fn(): ?Stadium => Stadium::fromName('競艇場'))
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Converter\ConverterDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->converter->ghost();
    }
}
