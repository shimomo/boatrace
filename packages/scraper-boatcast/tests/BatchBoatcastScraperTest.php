<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests;

use Boatrace\BoatcastScraper\BatchBoatcastScraper;
use Boatrace\Core\CoreContainer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class BatchBoatcastScraperTest extends TestCase
{
    /**
     * @return void
     */
    #[\Override]
    public static function setUpBeforeClass(): void
    {
        CoreContainer::addDefinitions(__DIR__ . '/fixtures/definitions.php');
    }

    /**
     * @return void
     */
    #[Test]
    public function setShowProgressUpdatesSharedProgress(): void
    {
        BatchBoatcastScraper::setShowProgress(true);

        $this->assertTrue(BatchBoatcastScraper::getShowProgress()->getValue());

        BatchBoatcastScraper::setShowProgress(false);

        $this->assertFalse(BatchBoatcastScraper::getShowProgress()->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeTimeReturnsResponse(): void
    {
        $response = BatchBoatcastScraper::scrapeTime(
            '2026-08-01',
            [22],
            [1],
            MockBrowser::createSequence([
                ['holding.json', 200],
                ['oriten-ashiya.txt', 200],
            ])
        )->getValue();

        $this->assertIsArray($response);
        $this->assertSame([22], array_keys($response));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeOddsReturnsResponse(): void
    {
        $response = BatchBoatcastScraper::scrapeOdds(
            '2026-08-01',
            [22],
            [1],
            MockBrowser::createSequence([
                ['holding.json', 200],
                ['odds-od1.txt', 200],
                ['odds-od2.txt', 200],
                ['odds-od3.txt', 200],
            ])
        )->getValue();

        $this->assertIsArray($response);
        $this->assertSame([22], array_keys($response));
    }
}
