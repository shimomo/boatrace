<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Throttler;

use Boatrace\Support\Throttler\Throttler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ThrottlerTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function getMinCallIntervalSecondsReturnsSeconds(): void
    {
        Throttler::setMinCallIntervalSeconds(3.0);

        $this->assertSame(3.0, Throttler::getMinCallIntervalSeconds()->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function setMinCallIntervalSecondsUpdatesSharedInstance(): void
    {
        Throttler::setMinCallIntervalSeconds(4.0);

        $this->assertSame(4.0, Throttler::getMinCallIntervalSeconds()->getValue());

        Throttler::setMinCallIntervalSeconds(3.0);
    }

    /**
     * @return void
     */
    #[Test]
    public function setMinCallIntervalSecondsReturnsResponseWithoutValue(): void
    {
        $this->assertNull(Throttler::setMinCallIntervalSeconds(3.0)->getValue());
    }
}
