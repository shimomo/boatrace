<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests;

use BadMethodCallException;
use Boatrace\BoatcastScraper\BoatcastScraperDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @author shimomo
 */
final class BoatcastScraperDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\BoatcastScraper\BoatcastScraperDispatcher
     */
    protected BoatcastScraperDispatcher $boatcastScraper;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->boatcastScraper = BoatcastScraperFactory::createBoatcastDispatcher();
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeTimeDelegatesToTimeScraper(): void
    {
        $response = $this->boatcastScraper->scrapeTime(
            '2026-08-05',
            21,
            1,
            MockBrowser::create('oriten-ashiya.txt')
        );

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertIsArray($racers[1]);
        $this->assertSame(36.88, $racers[1]['lap_time']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeVoteDelegatesToVoteScraper(): void
    {
        $response = $this->boatcastScraper->scrapeVote(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                ['vote-hyousu1.txt', 200],
                ['vote-hyousu2.txt', 200],
                ['vote-hyousu3.txt', 200],
            ])
        );

        $totals = $response['totals'];

        $this->assertIsArray($totals);
        $this->assertIsArray($totals['trifecta']);
        $this->assertSame(178529, $totals['trifecta']['total']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeOddsDelegatesToOddsScraper(): void
    {
        $response = $this->boatcastScraper->scrapeOdds(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                ['odds-od1.txt', 200],
                ['odds-od2.txt', 200],
                ['odds-od3.txt', 200],
            ])
        );

        $trifecta = $response['trifecta'];

        $this->assertIsArray($trifecta);
        $this->assertIsArray($trifecta[1]);
        $this->assertIsArray($trifecta[1][2]);
        $this->assertSame(6.9, $trifecta[1][2][3]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeOddsThrowsValueErrorWhenGivenInvalidStadiumNumber(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$stadiumNumber must be between 1 and 24, 25 given.');

        /** @psalm-suppress InvalidArgument */
        $this->boatcastScraper->scrapeOdds('2026-08-01', 25, 1, MockBrowser::create('odds-od1.txt'));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeVoteThrowsValueErrorWhenGivenInvalidRaceNumber(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$raceNumber must be between 1 and 12, 13 given.');

        /** @psalm-suppress InvalidArgument */
        $this->boatcastScraper->scrapeVote('2026-08-01', 22, 13, MockBrowser::create('vote-hyousu1.txt'));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeTimeThrowsValueErrorWhenGivenInvalidStadiumNumber(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$stadiumNumber must be between 1 and 24, 25 given.');

        /** @psalm-suppress InvalidArgument */
        $this->boatcastScraper->scrapeTime('2026-08-05', 25, 1, MockBrowser::create('oriten-ashiya.txt'));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeTimeThrowsValueErrorWhenGivenInvalidRaceNumber(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$raceNumber must be between 1 and 12, 13 given.');

        /** @psalm-suppress InvalidArgument */
        $this->boatcastScraper->scrapeTime('2026-08-05', 21, 13, MockBrowser::create('oriten-ashiya.txt'));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\BoatcastScraper\BoatcastScraperDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->boatcastScraper->ghost();
    }
}
