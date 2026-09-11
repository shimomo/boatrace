<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests\Scrapers;

use BadMethodCallException;
use Boatrace\BoatcastScraper\Tests\BoatcastScraperFactory;
use Boatrace\BoatcastScraper\Tests\MockBrowser;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class TimeScraperTest extends TestCase
{
    /**
     * @param non-empty-string $fixture
     * @param int<1, 24> $stadiumNumber
     * @param array<non-empty-string, int|float|string|null> $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TimeScraperDataProvider::class, 'scrapeProvider')]
    public function scrapeNormalizesLabelsToKeys(string $fixture, int $stadiumNumber, array $expected): void
    {
        $response = BoatcastScraperFactory::createTimeScraper()
            ->scrape('2026-08-05', $stadiumNumber, 1, MockBrowser::create($fixture));

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertSame($expected, $racers[1]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsRace(): void
    {
        $response = BoatcastScraperFactory::createTimeScraper()
            ->scrape('2026-08-05', 21, 1, MockBrowser::create('oriten-ashiya.txt'));

        $this->assertSame('2026-08-05', $response['date']);
        $this->assertSame(21, $response['stadium_number']);
        $this->assertSame(1, $response['race_number']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsSixRacers(): void
    {
        $response = BoatcastScraperFactory::createTimeScraper()
            ->scrape('2026-08-05', 21, 1, MockBrowser::create('oriten-ashiya.txt'));

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertSame([1, 2, 3, 4, 5, 6], array_keys($racers));
        $this->assertIsArray($racers[6]);
        $this->assertSame('小林 孝彰', $racers[6]['name']);
        $this->assertSame(36.73, $racers[6]['lap_time']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsEmptyRacersWhenFileIsMissing(): void
    {
        $response = BoatcastScraperFactory::createTimeScraper()
            ->scrape('2026-08-05', 3, 1, MockBrowser::create(null, 403));

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertSame([
            'entry_number' => 1,
            'name' => null,
            'lap_time' => null,
            'half_lap_time' => null,
            'turn_time' => null,
            'straight_time' => null,
        ], $racers[1]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsEmptyRacersWhenNotMeasured(): void
    {
        $response = BoatcastScraperFactory::createTimeScraper()
            ->scrape('2026-08-05', 21, 1, MockBrowser::create('oriten-unmeasured.txt'));

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertCount(6, $racers);
        $this->assertIsArray($racers[1]);
        $this->assertNull($racers[1]['name']);
        $this->assertNull($racers[1]['lap_time']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsEmptyRacersWhenSentinelIsMissing(): void
    {
        $response = BoatcastScraperFactory::createTimeScraper()
            ->scrape('2026-08-05', 21, 1, MockBrowser::create(null));

        $racers = $response['racers'];

        $this->assertIsArray($racers);
        $this->assertIsArray($racers[1]);
        $this->assertNull($racers[1]['lap_time']);
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\BoatcastScraper\Scrapers\TimeScraper::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        BoatcastScraperFactory::createTimeScraper()->ghost();
    }
}
