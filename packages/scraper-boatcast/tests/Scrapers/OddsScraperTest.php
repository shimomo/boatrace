<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests\Scrapers;

use BadMethodCallException;
use Boatrace\BoatcastScraper\Tests\BoatcastScraperFactory;
use Boatrace\BoatcastScraper\Tests\MockBrowser;
use Boatrace\Types\Enums\Absence;
use PHPUnit\Framework\Attributes\DataProviderExternal;
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
        self::$cache ??= BoatcastScraperFactory::createOddsScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                ['odds-od1.txt', 200],
                ['odds-od2.txt', 200],
                ['odds-od3.txt', 200],
            ])
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
            'is_fixed',
            'sources',
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
     * @param non-empty-string $bettingMethod
     * @param non-empty-list<int<1, 6>> $combination
     * @param float $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(OddsScraperDataProvider::class, 'oddsProvider')]
    public function scrapeReturnsOdds(string $bettingMethod, array $combination, float $expected): void
    {
        $this->assertSame($expected, $this->pluck($this->response[$bettingMethod], $combination));
    }

    /**
     * @param non-empty-string $bettingMethod
     * @param non-empty-list<int<1, 6>> $combination
     * @param float $lowerLimit
     * @param float $upperLimit
     * @return void
     */
    #[Test]
    #[DataProviderExternal(OddsScraperDataProvider::class, 'rangedOddsProvider')]
    public function scrapeReturnsRangedOdds(
        string $bettingMethod,
        array $combination,
        float $lowerLimit,
        float $upperLimit
    ): void {
        $this->assertSame(
            ['lower_limit' => $lowerLimit, 'upper_limit' => $upperLimit],
            $this->pluck($this->response[$bettingMethod], $combination)
        );
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
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsAllCombinations(): void
    {
        $this->assertSame(120, $this->countOdds($this->response['trifecta']));
        $this->assertSame(20, $this->countOdds($this->response['trio']));
        $this->assertSame(30, $this->countOdds($this->response['exacta']));
        $this->assertSame(15, $this->countOdds($this->response['quinella']));
        $this->assertSame(15, $this->countOdds($this->response['quinella_place']));
        $this->assertSame(6, $this->countOdds($this->response['win']));
        $this->assertSame(6, $this->countOdds($this->response['place']));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsFixedFlag(): void
    {
        $this->assertTrue($this->response['is_fixed']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeFallsBackToOnSaleFile(): void
    {
        $response = BoatcastScraperFactory::createOddsScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                [null, 403],
                ['odds-od1.txt', 200],
                [null, 403],
                ['odds-od2.txt', 200],
                [null, 403],
                ['odds-od3.txt', 200],
            ])
        );

        $this->assertFalse($response['is_fixed']);
        $this->assertSame(6.9, $this->pluck($response['trifecta'], [1, 2, 3]));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsEmptyOddsWhenNotOnSale(): void
    {
        $response = BoatcastScraperFactory::createOddsScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence(array_fill(0, 6, [null, 403]))
        );

        $this->assertSame(120, $this->countOdds($response['trifecta']));
        $this->assertNull($this->pluck($response['trifecta'], [1, 2, 3]));
        $this->assertSame(15, $this->countOdds($response['quinella_place']));
        $this->assertSame(
            ['lower_limit' => null, 'upper_limit' => null],
            $this->pluck($response['quinella_place'], [1, 2])
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsWhyEachFileWasEmpty(): void
    {
        $response = BoatcastScraperFactory::createOddsScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                [null, 403],
                ['before-sale.txt', 200],
                ['cancelled.txt', 200],
            ]),
            true
        );

        /** @var array<int, array{reason: ?int}> $sources */
        $sources = $response['sources'];

        $this->assertSame(Absence::未公開->value, $sources[1]['reason']);
        $this->assertSame(Absence::発売前->value, $sources[2]['reason']);
        $this->assertSame(Absence::中止->value, $sources[3]['reason']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReportsAnUnexpectedBodyAsUnexpected(): void
    {
        $response = BoatcastScraperFactory::createOddsScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                ['unexpected.txt', 200],
                ['unexpected.txt', 200],
                ['unexpected.txt', 200],
            ]),
            true
        );

        /** @var array<int, array{reason: ?int}> $sources */
        $sources = $response['sources'];

        $this->assertSame(Absence::想定外->value, $sources[1]['reason']);
        $this->assertSame(Absence::想定外->value, $sources[2]['reason']);
        $this->assertSame(Absence::想定外->value, $sources[3]['reason']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsNoReasonWhenOddsWereRead(): void
    {
        /** @var array<int, array{reason: ?int}> $sources */
        $sources = $this->response['sources'];

        $this->assertNull($sources[1]['reason']);
        $this->assertNull($sources[2]['reason']);
        $this->assertNull($sources[3]['reason']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsNullForRefundedCombinations(): void
    {
        $response = BoatcastScraperFactory::createOddsScraper()->scrape(
            '2026-08-06',
            9,
            7,
            MockBrowser::createSequence([
                ['odds-refunded-od1.txt', 200],
                ['odds-refunded-od2.txt', 200],
                ['odds-refunded-od3.txt', 200],
            ])
        );

        $this->assertNull($this->pluck($response['trifecta'], [1, 2, 3]));
        $this->assertNull($this->pluck($response['trifecta'], [1, 2, 6]));
        $this->assertSame(3.6, $this->pluck($response['trifecta'], [1, 2, 4]));

        $this->assertNull($this->pluck($response['trio'], [1, 2, 3]));
        $this->assertSame(1.1, $this->pluck($response['trio'], [1, 2, 4]));

        $this->assertNull($this->pluck($response['exacta'], [3, 1]));
        $this->assertSame(2.5, $this->pluck($response['exacta'], [1, 2]));

        $this->assertNull($this->pluck($response['win'], [3]));
        $this->assertSame(1.4, $this->pluck($response['win'], [1]));

        $this->assertSame(
            ['lower_limit' => null, 'upper_limit' => null],
            $this->pluck($response['quinella_place'], [1, 3])
        );
        $this->assertSame(
            ['lower_limit' => 1.2, 'upper_limit' => 1.9],
            $this->pluck($response['quinella_place'], [1, 4])
        );

        $this->assertSame(
            ['lower_limit' => null, 'upper_limit' => null],
            $this->pluck($response['place'], [3])
        );
        $this->assertSame(
            ['lower_limit' => 1.1, 'upper_limit' => 2.3],
            $this->pluck($response['place'], [1])
        );

        $this->assertSame(120, $this->countOdds($response['trifecta']));
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsNullWhileOnSaleWithoutVotes(): void
    {
        $response = BoatcastScraperFactory::createOddsScraper()->scrape(
            '2026-08-07',
            2,
            1,
            MockBrowser::createSequence([
                [null, 403],
                ['odds-onsale-od1.txt', 200],
                [null, 403],
                ['odds-onsale-od2.txt', 200],
                [null, 403],
                ['odds-onsale-od3.txt', 200],
            ])
        );

        $this->assertFalse($response['is_fixed']);

        $this->assertNull($this->pluck($response['trifecta'], [1, 2, 3]));
        $this->assertNull($this->pluck($response['trio'], [1, 2, 3]));
        $this->assertNull($this->pluck($response['exacta'], [1, 2]));
        $this->assertNull($this->pluck($response['quinella'], [1, 2]));
        $this->assertNull($this->pluck($response['win'], [1]));
        $this->assertSame(
            ['lower_limit' => null, 'upper_limit' => null],
            $this->pluck($response['quinella_place'], [1, 2])
        );
        $this->assertSame(
            ['lower_limit' => null, 'upper_limit' => null],
            $this->pluck($response['place'], [1])
        );

        $this->assertSame(120, $this->countOdds($response['trifecta']));
        $this->assertSame(30, $this->countOdds($response['exacta']));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\BoatcastScraper\Scrapers\OddsScraper::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        BoatcastScraperFactory::createOddsScraper()->ghost();
    }

    /**
     * @param mixed $odds
     * @param non-empty-list<int<1, 6>> $combination
     * @return mixed
     */
    private function pluck(mixed $odds, array $combination): mixed
    {
        foreach ($combination as $entryNumber) {
            $this->assertIsArray($odds);

            /** @var mixed $odds */
            $odds = $odds[$entryNumber];
        }

        return $odds;
    }

    /**
     * @param mixed $odds
     * @return int
     */
    private function countOdds(mixed $odds): int
    {
        if (!is_array($odds)) {
            return 0;
        }

        if (array_key_exists('lower_limit', $odds)) {
            return 1;
        }

        $count = 0;

        /** @var mixed $value */
        foreach ($odds as $value) {
            $count += is_array($value) ? $this->countOdds($value) : 1;
        }

        return $count;
    }
}
