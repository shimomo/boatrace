<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Converter;

use Boatrace\Support\Converter\Converter;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ConverterTest extends TestCase
{
    /**
     * @param int|float|string $arguments
     * @param int $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(ConverterDataProvider::class, 'toIntProvider')]
    public function toIntReturnsInt(int|float|string $arguments, int $expected): void
    {
        $this->assertSame($expected, Converter::toInt($arguments)->getValue());
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
        $this->assertSame($expected, Converter::toFloat($arguments)->getValue());
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
        $this->assertSame($expected, Converter::toKana($arguments)->getValue());
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
        $this->assertSame($expected, Converter::toDayNumber($arguments)->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function toCamelCaseKeysReturnsCamelCasedKeys(): void
    {
        $this->assertSame(
            ['stadiumNumber' => 1, 'raceNumber' => 2],
            Converter::toCamelCaseKeys(['stadium_number' => 1, 'race_number' => 2])->getValue()
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function toIntReturnsNullWhenGivenNull(): void
    {
        $this->assertNull(Converter::toInt(null)->getValue());
    }
}
