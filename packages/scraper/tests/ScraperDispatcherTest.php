<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests;

use BadMethodCallException;
use Boatrace\Scraper\ScraperDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @author shimomo
 */
final class ScraperDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Scraper\ScraperDispatcher
     */
    protected ScraperDispatcher $scraper;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->scraper = ScraperFactory::createScraperDispatcher();
    }

    /**
     * @return void
     */
    #[Test]
    public function getStadiumNumbersReturnsAllStadiumNumbers(): void
    {
        $this->assertSame(range(1, 24), $this->scraper->getStadiumNumbers());
    }

    /**
     * @return void
     */
    #[Test]
    public function getRaceNumbersReturnsAllRaceNumbers(): void
    {
        $this->assertSame(range(1, 12), $this->scraper->getRaceNumbers());
    }

    /**
     * @return void
     */
    #[Test]
    public function setMinCallIntervalSecondsUpdatesThrottler(): void
    {
        $this->scraper->setMinCallIntervalSeconds(5.0);

        $this->assertSame(5.0, $this->scraper->getMinCallIntervalSeconds());
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeProgramThrowsValueErrorWhenGivenInvalidStadiumNumber(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$stadiumNumber must be between 1 and 24, 25 given.');

        /** @psalm-suppress InvalidArgument */
        $this->scraper->scrapeProgram('2026-08-01', 25, 1, MockBrowser::create('program.html'));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeProgramThrowsValueErrorWhenGivenInvalidRaceNumber(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$raceNumber must be between 1 and 12, 13 given.');

        /** @psalm-suppress InvalidArgument */
        $this->scraper->scrapeProgram('2026-08-01', 22, 13, MockBrowser::create('program.html'));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeProgramDelegatesToProgramScraper(): void
    {
        $response = $this->scraper->scrapeProgram('2026-08-01', 22, 1, MockBrowser::create('program.html'));

        $this->assertSame('西部ボートレース記者クラブ杯', $response['title']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeStadiumDelegatesToStadiumScraper(): void
    {
        $response = $this->scraper->scrapeStadium('2026-08-01', MockBrowser::create('stadium.html'));

        $this->assertSame('福岡', $response[22] ?? null);
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Scraper\ScraperDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->scraper->ghost();
    }
}
