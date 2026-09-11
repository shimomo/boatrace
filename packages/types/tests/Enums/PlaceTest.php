<?php

declare(strict_types=1);

namespace Boatrace\Types\Tests\Enums;

use Boatrace\Types\Enums\Place;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class PlaceTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function fromShortNameReturnsOther(): void
    {
        $this->assertSame(Place::その他, Place::fromShortName('_'));
        $this->assertSame(99, Place::その他->value);
    }

    /**
     * @return void
     */
    #[Test]
    public function fromShortNameReturnsPlace(): void
    {
        $this->assertSame(Place::一着, Place::fromShortName('1'));
        $this->assertSame(Place::フライング, Place::fromShortName('F'));
        $this->assertSame(Place::出遅れ, Place::fromShortName('L'));
    }

    /**
     * @return void
     */
    #[Test]
    public function toArrayReturnsAllPlaces(): void
    {
        $this->assertCount(17, Place::toArray());
    }
}
