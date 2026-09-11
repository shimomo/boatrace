<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Validator;

use Boatrace\Support\Validator\Validator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @author shimomo
 */
final class ValidatorTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function betweenReturnsValue(): void
    {
        $this->assertSame(24, Validator::between(24, 1, 24)->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function betweenThrowsValueErrorWhenValueIsOutOfRange(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$raceNumber must be between 1 and 12, 13 given.');

        Validator::between(13, 1, 12, '$raceNumber');
    }
}
