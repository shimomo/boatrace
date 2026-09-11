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
final class OddsScraperTest extends TestCase
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
        self::$cache ??= ScraperFactory::createOddsScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::create(
                'odds3t.html',
                'odds3f.html',
                'odds2tf.html',
                'oddsk.html',
                'oddstf.html'
            )
        );

        $this->response = self::$cache;
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsAllBettingMethods(): void
    {
        $this->assertSame([
            'date',
            'stadium_number',
            'race_number',
            'trifecta',
            'trio',
            'exacta',
            'quinella',
            'quinella_place',
            'win',
            'place',
        ], array_keys($this->response));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsTrifectaOdds(): void
    {
        $trifecta = $this->response['trifecta'];

        $this->assertIsArray($trifecta);
        $this->assertIsArray($trifecta[1]);
        $this->assertIsArray($trifecta[1][2]);
        $this->assertSame(6.9, $trifecta[1][2][3]);
        $this->assertCount(6, $trifecta);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsTrioAndExactaAndQuinellaOdds(): void
    {
        $trio = $this->response['trio'];
        $exacta = $this->response['exacta'];
        $quinella = $this->response['quinella'];

        $this->assertIsArray($trio);
        $this->assertIsArray($trio[1]);
        $this->assertIsArray($trio[1][2]);
        $this->assertSame(3.8, $trio[1][2][3]);

        $this->assertIsArray($exacta);
        $this->assertIsArray($exacta[1]);
        $this->assertSame(2.3, $exacta[1][2]);

        $this->assertIsArray($quinella);
        $this->assertIsArray($quinella[1]);
        $this->assertSame(1.7, $quinella[1][2]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsRangedOdds(): void
    {
        $quinellaPlace = $this->response['quinella_place'];
        $place = $this->response['place'];

        $this->assertIsArray($quinellaPlace);
        $this->assertIsArray($quinellaPlace[1]);
        $this->assertSame(['lower_limit' => 1.7, 'upper_limit' => 2.4], $quinellaPlace[1][2]);

        $this->assertIsArray($place);
        $this->assertSame(['lower_limit' => 1.0, 'upper_limit' => 1.0], $place[1]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsWinOdds(): void
    {
        $win = $this->response['win'];

        $this->assertIsArray($win);
        $this->assertSame(1.1, $win[1]);
        $this->assertCount(6, $win);
    }
}
