<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests\Scrapers;

use Boatrace\Scraper\Tests\MockBrowser;
use Boatrace\Scraper\Tests\ScraperFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ResultScraperTest extends TestCase
{
    /**
     * @var ?array<non-empty-string, mixed>
     */
    private static ?array $cache = null;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var array<non-empty-string, mixed>
     */
    protected array $response;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        self::$cache ??= ScraperFactory::createResultScraper()
            ->scrape('2026-08-01', 22, 1, MockBrowser::create('result.html'));

        $this->response = self::$cache;
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsRace(): void
    {
        $this->assertSame('2026-08-01', $this->response['date']);
        $this->assertSame(22, $this->response['stadium_number']);
        $this->assertSame(1, $this->response['race_number']);
        $this->assertSame(1, $this->response['weather_number']);
        $this->assertSame(3, $this->response['wind_speed']);
        $this->assertSame(8, $this->response['wind_direction_number']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsRacersWithPlace(): void
    {
        $racers = $this->response['racers'];

        $this->assertIsArray($racers);
        $this->assertCount(6, $racers);
        $this->assertIsArray($racers[1]);
        $this->assertSame(4036, $racers[1]['number']);
        $this->assertSame('金田 諭', $racers[1]['name']);
        $this->assertSame(1, $racers[1]['place_number']);
        $this->assertSame(0.14, $racers[1]['start_timing']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsSameKeysForEveryRacer(): void
    {
        $racers = $this->response['racers'];

        $this->assertIsArray($racers);
        $this->assertIsArray($racers[1]);

        $keys = array_keys($racers[1]);

        foreach (range(1, 6) as $entryNumber) {
            $this->assertIsArray($racers[$entryNumber]);
            $this->assertSame($keys, array_keys($racers[$entryNumber]));
        }
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsEmptyRacersWhenResultTableIsMissing(): void
    {
        $response = ScraperFactory::createResultScraper()
            ->scrape('2026-08-01', 22, 1, MockBrowser::create('program.html'));

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertCount(6, $racers);
        $this->assertSame([
            'entry_number' => 1,
            'course_number' => null,
            'start_timing_source' => null,
            'start_timing' => null,
            'place_number_source' => null,
            'place_number' => null,
            'number_source' => null,
            'number' => null,
            'name' => null,
        ], $racers[1]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsSpecialPayout(): void
    {
        $response = ScraperFactory::createResultScraper()
            ->scrape('2026-07-29', 17, 11, MockBrowser::create('result-special-payout.html'));

        $payouts = $response['payouts'];

        $this->assertIsArray($payouts);
        $this->assertSame(
            [['combination' => null, 'amount' => 70, 'label' => '特払']],
            $payouts['win']
        );
        $this->assertSame(
            [['combination' => '6-1-2', 'amount' => 65790, 'label' => null]],
            $payouts['trifecta']
        );
        $this->assertSame(
            [
                ['combination' => '6', 'amount' => 1360, 'label' => null],
                ['combination' => '1', 'amount' => 360, 'label' => null],
            ],
            $payouts['place']
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsVoidPayout(): void
    {
        $response = ScraperFactory::createResultScraper()
            ->scrape('2026-07-22', 9, 10, MockBrowser::create('result-void-payout.html'));

        $payouts = $response['payouts'];

        $this->assertIsArray($payouts);
        $this->assertSame(
            [['combination' => null, 'amount' => 100, 'label' => '不成立']],
            $payouts['trifecta']
        );
        $this->assertSame(
            [['combination' => null, 'amount' => 100, 'label' => '不成立']],
            $payouts['quinella_place']
        );
        $this->assertSame(
            [['combination' => '3-2', 'amount' => 150, 'label' => null]],
            $payouts['exacta']
        );
        $this->assertSame(
            [['combination' => '3', 'amount' => 180, 'label' => null]],
            $payouts['win']
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsRefundsAndRemarks(): void
    {
        $response = ScraperFactory::createResultScraper()
            ->scrape('2026-07-22', 9, 10, MockBrowser::create('result-void-payout.html'));

        $this->assertSame([1, 4, 5, 6], $response['refunds']);
        $this->assertSame('【返還艇あり】', $response['remarks']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsEmptyRefundsWhenNoBoatIsRefunded(): void
    {
        $this->assertSame([], $this->response['refunds']);
        $this->assertNull($this->response['remarks']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsPayouts(): void
    {
        $payouts = $this->response['payouts'];

        $this->assertIsArray($payouts);
        $this->assertSame(
            [['combination' => '1-2-3', 'amount' => 690, 'label' => null]],
            $payouts['trifecta']
        );
        $this->assertSame(
            [
                ['combination' => '1=2', 'amount' => 210, 'label' => null],
                ['combination' => '1=3', 'amount' => 170, 'label' => null],
                ['combination' => '2=3', 'amount' => 670, 'label' => null],
            ],
            $payouts['quinella_place']
        );
        $this->assertArrayHasKey('trio', $payouts);
        $this->assertArrayHasKey('exacta', $payouts);
        $this->assertArrayHasKey('quinella', $payouts);
        $this->assertArrayHasKey('win', $payouts);
        $this->assertArrayHasKey('place', $payouts);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeKeepsPayoutWithoutAmount(): void
    {
        $response = ScraperFactory::createResultScraper()
            ->scrape('2026-08-01', 22, 1, MockBrowser::create('result-missing-amount.html'));

        $payouts = $response['payouts'];

        $this->assertIsArray($payouts);
        $this->assertSame(
            [['combination' => '1-2-3', 'amount' => null, 'label' => null]],
            $payouts['trifecta']
        );
        $this->assertSame(
            [['combination' => '1=2=3', 'amount' => 380, 'label' => null]],
            $payouts['trio']
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeKeepsPayoutWithoutVote(): void
    {
        $response = ScraperFactory::createResultScraper()
            ->scrape('2026-08-04', 12, 3, MockBrowser::create('result-no-vote-payout.html'));

        $payouts = $response['payouts'];

        $this->assertIsArray($payouts);
        $this->assertSame(
            [
                ['combination' => '4', 'amount' => 300, 'label' => null],
                ['combination' => '5', 'amount' => null, 'label' => null],
            ],
            $payouts['place']
        );
        $this->assertSame(
            [
                ['combination' => '4=5', 'amount' => 920, 'label' => null],
                ['combination' => '4=6', 'amount' => 1140, 'label' => null],
                ['combination' => '5=6', 'amount' => null, 'label' => null],
            ],
            $payouts['quinella_place']
        );
        $this->assertSame(
            [['combination' => '4-5-6', 'amount' => 18510, 'label' => null]],
            $payouts['trifecta']
        );
    }
}
