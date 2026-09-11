<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests;

use BadMethodCallException;
use Boatrace\Scraper\BatchScraperDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class BatchScraperDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Scraper\BatchScraperDispatcher
     */
    protected BatchScraperDispatcher $batchScraper;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->batchScraper = ScraperFactory::createBatchScraperDispatcher();
    }

    /**
     * @return void
     */
    #[Test]
    public function setShowProgressUpdatesProgress(): void
    {
        $this->assertFalse($this->batchScraper->getShowProgress());

        $this->batchScraper->setShowProgress(true);

        $this->assertTrue($this->batchScraper->getShowProgress());
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeProgramScrapesActiveStadiumsOnly(): void
    {
        $response = $this->batchScraper->scrapeProgram(
            '2026-08-01',
            [21, 22],
            [1],
            MockBrowser::create('stadium.html', 'program.html')
        );

        $this->assertSame([22], array_keys($response));
        $this->assertSame([1], array_keys($response[22]));
        $this->assertSame('西部ボートレース記者クラブ杯', $response[22][1]['title']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeResultScrapesActiveStadiumsOnly(): void
    {
        $response = $this->batchScraper->scrapeResult(
            '2026-08-01',
            [22],
            [1],
            MockBrowser::create('stadium.html', 'result.html')
        );

        $this->assertSame([22], array_keys($response));

        $payouts = $response[22][1]['payouts'];

        $this->assertIsArray($payouts);
        $this->assertIsArray($payouts['trifecta']);
        $this->assertIsArray($payouts['trifecta'][0]);
        $this->assertSame(690, $payouts['trifecta'][0]['amount']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeProgramReturnsEmptyArrayWhenNoStadiumIsActive(): void
    {
        $response = $this->batchScraper->scrapeProgram(
            '2026-08-01',
            [21],
            [1],
            MockBrowser::create('stadium.html')
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
            'Call to undefined method `Boatrace\Scraper\BatchScraperDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->batchScraper->ghost();
    }
}
