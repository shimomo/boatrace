<?php

declare(strict_types=1);

namespace Boatrace\Types\Tests\Enums;

use Boatrace\Types\Enums\Part;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @author shimomo
 */
final class PartTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function fromShortNameReturnsPart(): void
    {
        $this->assertSame(Part::ピストン, Part::fromShortName('ピストン'));
        $this->assertSame(Part::ピストンリング, Part::fromShortName('リング'));
        $this->assertSame(Part::キャリアボデー, Part::fromShortName('キャリボ'));
    }

    /**
     * @return void
     */
    #[Test]
    public function fromNameReturnsPart(): void
    {
        $this->assertSame(Part::電気一式, Part::fromName('電気一式'));
        $this->assertSame(Part::クランクシャフト, Part::fromName('クランクシャフト'));
    }

    /**
     * @return void
     */
    #[Test]
    public function fromShortNameThrowsValueErrorWhenShortNameIsUnknown(): void
    {
        $this->expectException(ValueError::class);

        Part::fromShortName('プロペラ');
    }

    /**
     * @return void
     */
    #[Test]
    public function toArrayReturnsAllParts(): void
    {
        $this->assertCount(8, Part::toArray());

        $this->assertSame([
            'number' => 8,
            'name' => 'キャリアボデー',
            'short_name' => 'キャリボ',
        ], Part::toArray()[7]);
    }
}
