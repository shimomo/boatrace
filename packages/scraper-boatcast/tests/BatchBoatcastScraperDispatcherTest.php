<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests;

use BadMethodCallException;
use Boatrace\BoatcastScraper\BatchBoatcastScraperDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class BatchBoatcastScraperDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\BoatcastScraper\BatchBoatcastScraperDispatcher
     */
    protected BatchBoatcastScraperDispatcher $batchBoatcastScraper;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->batchBoatcastScraper = BoatcastScraperFactory::createBatchBoatcastDispatcher();
    }

    /**
     * @return void
     */
    #[Test]
    public function setShowProgressUpdatesProgress(): void
    {
        $this->assertFalse($this->batchBoatcastScraper->getShowProgress());

        $this->batchBoatcastScraper->setShowProgress(true);

        $this->assertTrue($this->batchBoatcastScraper->getShowProgress());
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeTimeScrapesActiveStadiumsOnly(): void
    {
        $response = $this->batchBoatcastScraper->scrapeTime(
            '2026-08-01',
            [21, 22],
            [1],
            MockBrowser::createSequence([
                ['holding.json', 200],
                ['oriten-ashiya.txt', 200],
            ])
        );

        $this->assertSame([22], array_keys($response));
        $this->assertSame([1], array_keys($response[22]));
        $this->assertIsArray($response[22][1]['racers']);
        $this->assertIsArray($response[22][1]['racers'][1]);
        $this->assertSame(36.88, $response[22][1]['racers'][1]['lap_time']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeVoteScrapesActiveStadiumsOnly(): void
    {
        $response = $this->batchBoatcastScraper->scrapeVote(
            '2026-08-01',
            [22],
            [1],
            MockBrowser::createSequence([
                ['holding.json', 200],
                ['vote-hyousu1.txt', 200],
                ['vote-hyousu2.txt', 200],
                ['vote-hyousu3.txt', 200],
            ])
        );

        $this->assertSame([22], array_keys($response));
        $this->assertIsArray($response[22][1]['totals']);
        $this->assertIsArray($response[22][1]['totals']['trifecta']);
        $this->assertSame(178529, $response[22][1]['totals']['trifecta']['total']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeOddsScrapesActiveStadiumsOnly(): void
    {
        $response = $this->batchBoatcastScraper->scrapeOdds(
            '2026-08-01',
            [22],
            [1],
            MockBrowser::createSequence([
                ['holding.json', 200],
                ['odds-od1.txt', 200],
                ['odds-od2.txt', 200],
                ['odds-od3.txt', 200],
            ])
        );

        $this->assertSame([22], array_keys($response));
        $this->assertIsArray($response[22][1]['trifecta']);
        $this->assertIsArray($response[22][1]['trifecta'][1]);
        $this->assertIsArray($response[22][1]['trifecta'][1][2]);
        $this->assertSame(6.9, $response[22][1]['trifecta'][1][2][3]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeTimeReturnsEmptyArrayWhenNoStadiumIsActive(): void
    {
        $response = $this->batchBoatcastScraper->scrapeTime(
            '2026-08-01',
            [21],
            [1],
            MockBrowser::create('holding.json')
        );

        $this->assertSame([], $response);
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\BoatcastScraper\BatchBoatcastScraperDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->batchBoatcastScraper->ghost();
    }
}
