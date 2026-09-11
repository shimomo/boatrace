<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests\Scrapers;

use Boatrace\Scraper\Tests\MockBrowser;
use Boatrace\Scraper\Tests\ScraperFactory;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class PreviewScraperTest extends TestCase
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
        self::$cache ??= ScraperFactory::createPreviewScraper()
            ->scrape('2026-08-01', 22, 1, MockBrowser::create('preview.html'));

        $this->response = self::$cache;
    }

    /**
     * @param non-empty-string $key
     * @param int|float|string|null $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(PreviewScraperDataProvider::class, 'raceProvider')]
    public function scrapeReturnsRace(string $key, int|float|string|null $expected): void
    {
        $this->assertSame($expected, $this->response[$key] ?? null);
    }

    /**
     * @param non-empty-string $key
     * @param int|float|string|null $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(PreviewScraperDataProvider::class, 'racerProvider')]
    public function scrapeReturnsRacer(string $key, int|float|string|null $expected): void
    {
        $racers = $this->response['racers'];

        $this->assertIsArray($racers);
        $this->assertIsArray($racers[1]);
        $this->assertSame($expected, $racers[1][$key] ?? null);
    }

    /**
     * @param non-empty-string $fixture
     * @param non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param array<non-empty-string, mixed> $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(PreviewScraperDataProvider::class, 'weatherAsOfProvider')]
    public function scrapeReturnsWhenTheWeatherWasTaken(
        string $fixture,
        string $date,
        int $stadiumNumber,
        int $raceNumber,
        array $expected,
    ): void {
        $response = ScraperFactory::createPreviewScraper()
            ->scrape($date, $stadiumNumber, $raceNumber, MockBrowser::create($fixture));

        $this->assertSame($expected, array_intersect_key($response, $expected));
    }

    /**
     * @param int<1, 6> $entryNumber
     * @param ?string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(PreviewScraperDataProvider::class, 'propellerProvider')]
    public function scrapeReturnsPropeller(int $entryNumber, ?string $expected): void
    {
        $response = ScraperFactory::createPreviewScraper()
            ->scrape('2026-05-26', 6, 12, MockBrowser::create('preview-exchange.html'));

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertIsArray($racers[$entryNumber]);
        $this->assertSame($expected, $racers[$entryNumber]['propeller'] ?? null);
    }

    /**
     * @param int<1, 6> $entryNumber
     * @param list<array<non-empty-string, mixed>> $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(PreviewScraperDataProvider::class, 'partsProvider')]
    public function scrapeReturnsParts(int $entryNumber, array $expected): void
    {
        $response = ScraperFactory::createPreviewScraper()
            ->scrape('2026-05-26', 6, 12, MockBrowser::create('preview-exchange.html'));

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertIsArray($racers[$entryNumber]);
        $this->assertSame($expected, $racers[$entryNumber]['parts'] ?? null);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsEmptyPartsWhenPreviewIsNotPublished(): void
    {
        $response = ScraperFactory::createPreviewScraper()
            ->scrape('2026-08-07', 2, 1, MockBrowser::create('preview-unpublished.html'));

        $this->assertNull($response['weather_number']);
        $this->assertNull($response['wind_speed']);

        $racers = $response['racers'];

        $this->assertIsArray($racers);

        foreach (range(1, 6) as $entryNumber) {
            $this->assertIsArray($racers[$entryNumber]);
            $this->assertNull($racers[$entryNumber]['exhibition_time']);
            $this->assertNull($racers[$entryNumber]['propeller']);
            $this->assertSame([], $racers[$entryNumber]['parts']);
        }
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsNullPartsWhenCellIsMissing(): void
    {
        $racers = $this->response['racers'];

        $this->assertIsArray($racers);
        $this->assertIsArray($racers[1]);
        $this->assertSame([], $racers[1]['parts']);

        $response = ScraperFactory::createPreviewScraper()
            ->scrape('2026-08-01', 22, 1, MockBrowser::create('stadium.html'));

        $missingRacers = $response['racers'];

        $this->assertIsArray($missingRacers);
        $this->assertIsArray($missingRacers[1]);
        $this->assertNull($missingRacers[1]['parts']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsSameKeysForEveryRacer(): void
    {
        $racers = $this->response['racers'];

        $this->assertIsArray($racers);
        $this->assertSame([1, 2, 3, 4, 5, 6], array_keys($racers));
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
    public function scrapeReturnsEmptyRacersWhenPreviewTableIsMissing(): void
    {
        $response = ScraperFactory::createPreviewScraper()
            ->scrape('2026-08-01', 22, 1, MockBrowser::create('stadium.html'));

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertCount(6, $racers);
        $this->assertSame([
            'entry_number' => 3,
            'course_number' => null,
            'start_timing_source' => null,
            'start_timing' => null,
            'weight_source' => null,
            'weight' => null,
            'weight_adjustment_source' => null,
            'weight_adjustment' => null,
            'exhibition_time_source' => null,
            'exhibition_time' => null,
            'tilt_adjustment_source' => null,
            'tilt_adjustment' => null,
            'propeller' => null,
            'parts' => null,
        ], $racers[3]);
    }
}
