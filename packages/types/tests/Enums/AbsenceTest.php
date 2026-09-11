<?php

declare(strict_types=1);

namespace Boatrace\Types\Tests\Enums;

use Boatrace\Types\Enums\Absence;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class AbsenceTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function fromStatusReturnsNullWhileOnSale(): void
    {
        $this->assertNull(Absence::fromStatus('1'));
    }

    /**
     * @return void
     */
    #[Test]
    public function fromStatusReturnsBeforeSale(): void
    {
        $this->assertSame(Absence::発売前, Absence::fromStatus('0'));
        $this->assertSame(Absence::発売前, Absence::fromStatus('2'));
        $this->assertSame(Absence::発売前, Absence::fromStatus(''));
    }

    /**
     * @return void
     */
    #[Test]
    public function fromStatusReturnsCancelled(): void
    {
        $this->assertSame(Absence::中止, Absence::fromStatus('3'));
    }

    /**
     * @return void
     */
    #[Test]
    public function fromStatusReturnsUnexpectedForUnknownStatus(): void
    {
        $this->assertSame(Absence::想定外, Absence::fromStatus('9'));
        $this->assertSame(Absence::想定外, Absence::fromStatus(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function toArrayReturnsAllReasons(): void
    {
        $this->assertCount(4, Absence::toArray());

        $this->assertSame([
            'number' => 4,
            'name' => '中止',
        ], Absence::toArray()[3]);
    }
}
