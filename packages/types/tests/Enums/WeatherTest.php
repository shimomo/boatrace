<?php

declare(strict_types=1);

namespace Boatrace\Types\Tests\Enums;

use Boatrace\Types\Enums\Weather;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class WeatherTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function fromNameReturnsTyphoon(): void
    {
        $this->assertSame(6, Weather::fromName('台風')?->value);
    }

    /**
     * @return void
     */
    #[Test]
    public function fromNameReturnsOther(): void
    {
        $this->assertSame(99, Weather::fromName('その他')?->value);
    }

    /**
     * @return void
     */
    #[Test]
    public function shortNameReturnsShortName(): void
    {
        $this->assertSame('台', Weather::台風->shortName());
        $this->assertSame('他', Weather::その他->shortName());
    }

    /**
     * @return void
     */
    #[Test]
    public function fromShortNameReturnsWeather(): void
    {
        $this->assertSame(Weather::台風, Weather::fromShortName('台'));
        $this->assertSame(Weather::その他, Weather::fromShortName('他'));
    }

    /**
     * @return void
     */
    #[Test]
    public function toArrayReturnsAllWeathers(): void
    {
        $this->assertCount(7, Weather::toArray());
    }
}
