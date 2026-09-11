<?php

declare(strict_types=1);

namespace Boatrace\Types\Tests\Enums;

use Boatrace\Types\Enums\WindDirection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @author shimomo
 */
final class WindDirectionTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function fromValueReturnsWindDirection(): void
    {
        $this->assertSame(WindDirection::北, WindDirection::fromValue(1));
        $this->assertSame(WindDirection::北東, WindDirection::fromValue(3));
        $this->assertSame(WindDirection::無風, WindDirection::fromValue(17));
    }

    /**
     * @return void
     */
    #[Test]
    public function fromValueReturnsNullWhenGivenNull(): void
    {
        $this->assertNull(WindDirection::fromValue(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function fromValueThrowsValueErrorWhenGivenUnknownValue(): void
    {
        $this->expectException(ValueError::class);

        WindDirection::fromValue(18);
    }
}
