<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests;

use Boatrace\Scraper\Scraper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ScraperTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function getStadiumNumbersReturnsAllStadiumNumbers(): void
    {
        $this->assertSame(range(1, 24), Scraper::getStadiumNumbers()->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function getRaceNumbersReturnsAllRaceNumbers(): void
    {
        $this->assertSame(range(1, 12), Scraper::getRaceNumbers()->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function getMinCallIntervalSecondsReturnsThrottlerSeconds(): void
    {
        $this->assertIsFloat(Scraper::getMinCallIntervalSeconds()->getValue());
    }
}
