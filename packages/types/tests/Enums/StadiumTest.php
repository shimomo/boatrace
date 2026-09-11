<?php

declare(strict_types=1);

namespace Boatrace\Types\Tests\Enums;

use Boatrace\Types\Enums\Stadium;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @author shimomo
 */
final class StadiumTest extends TestCase
{
    /**
     * @param non-empty-string $arguments
     * @param int<1, 24> $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(StadiumDataProvider::class, 'fromNameProvider')]
    public function fromNameReturnsStadium(string $arguments, int $expected): void
    {
        $this->assertSame($expected, Stadium::fromName($arguments)?->value);
    }

    /**
     * @return void
     */
    #[Test]
    public function fromNameReturnsNullWhenGivenNull(): void
    {
        $this->assertNull(Stadium::fromName(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function fromNameThrowsValueErrorWhenGivenUnknownName(): void
    {
        $this->expectException(ValueError::class);

        Stadium::fromName('競艇場');
    }

    /**
     * @return void
     */
    #[Test]
    public function toArrayReturnsAllStadiums(): void
    {
        $stadiums = Stadium::toArray();

        $this->assertCount(24, $stadiums);
        $this->assertSame(['number' => 1, 'name' => '桐生'], $stadiums[0]);
        $this->assertSame(['number' => 24, 'name' => '大村'], $stadiums[23]);
    }
}
