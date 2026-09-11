<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests;

use Boatrace\BoatcastScraper\BoatcastScraper;
use Boatrace\Core\CoreContainer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class BoatcastScraperTest extends TestCase
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
    public function scrapeTimeReturnsResponse(): void
    {
        $response = BoatcastScraper::scrapeTime(
            '2026-08-05',
            21,
            1,
            MockBrowser::create('oriten-ashiya.txt')
        )->getValue();

        $this->assertIsArray($response);
        $this->assertSame(21, $response['stadium_number']);
        $this->assertIsArray($response['racers']);
        $this->assertIsArray($response['racers'][1]);
        $this->assertSame('井上 恵一', $response['racers'][1]['name']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeVoteReturnsResponse(): void
    {
        $response = BoatcastScraper::scrapeVote(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                ['vote-hyousu1.txt', 200],
                ['vote-hyousu2.txt', 200],
                ['vote-hyousu3.txt', 200],
            ])
        )->getValue();

        $this->assertIsArray($response);
        $this->assertTrue($response['is_fixed']);
        $this->assertIsArray($response['win']);
        $this->assertSame(127, $response['win'][1]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeOddsReturnsResponse(): void
    {
        $response = BoatcastScraper::scrapeOdds(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                ['odds-od1.txt', 200],
                ['odds-od2.txt', 200],
                ['odds-od3.txt', 200],
            ])
        )->getValue();

        $this->assertIsArray($response);
        $this->assertTrue($response['is_fixed']);
        $this->assertIsArray($response['win']);
        $this->assertSame(1.1, $response['win'][1]);
    }
}
