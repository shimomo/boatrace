<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests;

use Boatrace\Scraper\BatchScraper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class BatchScraperTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function setShowProgressUpdatesSharedProgress(): void
    {
        BatchScraper::setShowProgress(true);

        $this->assertTrue(BatchScraper::getShowProgress()->getValue());

        BatchScraper::setShowProgress(false);

        $this->assertFalse(BatchScraper::getShowProgress()->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeProgramReturnsEmptyArrayWhenNoStadiumIsActive(): void
    {
        $response = BatchScraper::scrapeProgram(
            '2026-08-01',
            [21],
            [1],
            MockBrowser::create('stadium.html')
        );

        $this->assertSame([], $response->getValue());
    }
}
