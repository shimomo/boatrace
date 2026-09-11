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
final class VoteScraperTest extends TestCase
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
        self::$cache ??= BoatcastScraperFactory::createVoteScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                ['vote-hyousu1.txt', 200],
                ['vote-hyousu2.txt', 200],
                ['vote-hyousu3.txt', 200],
            ])
        );

        $this->response = self::$cache;
    }

    /**
     * @param non-empty-string $bettingMethod
     * @param non-empty-list<int<1, 6>> $combination
     * @param int $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(VoteScraperDataProvider::class, 'voteProvider')]
    public function scrapeReturnsVotes(string $bettingMethod, array $combination, int $expected): void
    {
        /** @var mixed $votes */
        $votes = $this->response[$bettingMethod];

        foreach ($combination as $entryNumber) {
            $this->assertIsArray($votes);

            /** @var mixed $votes */
            $votes = $votes[$entryNumber];
        }

        $this->assertSame($expected, $votes);
    }

    /**
     * @param non-empty-string $bettingMethod
     * @param int $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(VoteScraperDataProvider::class, 'totalProvider')]
    public function scrapeReturnsTotals(string $bettingMethod, int $expected): void
    {
        $totals = $this->response['totals'];

        $this->assertIsArray($totals);
        $this->assertIsArray($totals[$bettingMethod]);
        $this->assertSame($expected, $totals[$bettingMethod]['total']);
        $this->assertSame(0, $totals[$bettingMethod]['refunded']);
        $this->assertSame($expected, $totals[$bettingMethod]['effective']);
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
        $trifecta = $this->response['trifecta'];
        $trio = $this->response['trio'];

        $this->assertIsArray($trifecta);
        $this->assertIsArray($trio);
        $this->assertSame(120, $this->countVotes($trifecta));
        $this->assertSame(20, $this->countVotes($trio));
        $this->assertSame(30, $this->countVotes($this->response['exacta']));
        $this->assertSame(15, $this->countVotes($this->response['quinella']));
        $this->assertSame(15, $this->countVotes($this->response['quinella_place']));
        $this->assertSame(6, $this->countVotes($this->response['win']));
        $this->assertSame(6, $this->countVotes($this->response['place']));
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
    public function scrapeReturnsSourceMetadataPerFile(): void
    {
        $response = BoatcastScraperFactory::createVoteScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                ['vote-hyousu1.txt', 200, [
                    'etag' => '"hyousu1"',
                    'last-modified' => 'Sat, 01 Aug 2026 03:19:05 GMT',
                ]],
                ['vote-hyousu2.txt', 200, [
                    'etag' => '"hyousu2"',
                    'last-modified' => 'Sat, 01 Aug 2026 03:20:11 GMT',
                ]],
                ['vote-hyousu3.txt', 200, []],
            ])
        );

        $this->assertSame([
            1 => [
                'is_fixed' => true,
                'etag' => '"hyousu1"',
                'last_modified' => '2026-08-01T03:19:05+00:00',
                'reason' => null,
            ],
            2 => [
                'is_fixed' => true,
                'etag' => '"hyousu2"',
                'last_modified' => '2026-08-01T03:20:11+00:00',
                'reason' => null,
            ],
            3 => [
                'is_fixed' => true,
                'etag' => null,
                'last_modified' => null,
                'reason' => null,
            ],
        ], $response['sources']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeTakesSourceMetadataFromTheBodyItUsed(): void
    {
        $response = BoatcastScraperFactory::createVoteScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                [null, 403, ['etag' => '"kakutei-not-used"']],
                ['vote-hyousu1.txt', 200, ['etag' => '"api"']],
                ['vote-hyousu2.txt', 200, ['etag' => '"hyousu2"']],
                ['vote-hyousu3.txt', 200, ['etag' => '"hyousu3"']],
            ])
        );

        /** @var array<int, array{is_fixed: bool, etag: ?string, last_modified: ?string}> $sources */
        $sources = $response['sources'];

        $this->assertSame('"api"', $sources[1]['etag']);
        $this->assertFalse($sources[1]['is_fixed']);
        $this->assertTrue($sources[2]['is_fixed']);
        $this->assertFalse($response['is_fixed']);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsWhyEachFileWasEmpty(): void
    {
        $response = BoatcastScraperFactory::createVoteScraper()->scrape(
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
        $response = BoatcastScraperFactory::createVoteScraper()->scrape(
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
    public function scrapeSkipsTheFixedFileWhenOnSaleOnly(): void
    {
        $response = BoatcastScraperFactory::createVoteScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                ['vote-hyousu1.txt', 200],
                ['vote-hyousu2.txt', 200],
                ['vote-hyousu3.txt', 200],
            ]),
            true
        );

        $this->assertFalse($response['is_fixed']);

        $trifecta = $response['trifecta'];

        $this->assertIsArray($trifecta);
        $this->assertIsArray($trifecta[1]);
        $this->assertIsArray($trifecta[1][2]);
        $this->assertSame(19241, $trifecta[1][2][3]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeFallsBackToOnSaleFile(): void
    {
        $response = BoatcastScraperFactory::createVoteScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence([
                [null, 403],
                ['vote-hyousu1.txt', 200],
                [null, 403],
                ['vote-hyousu2.txt', 200],
                [null, 403],
                ['vote-hyousu3.txt', 200],
            ])
        );

        $this->assertFalse($response['is_fixed']);

        $trifecta = $response['trifecta'];

        $this->assertIsArray($trifecta);
        $this->assertIsArray($trifecta[1]);
        $this->assertIsArray($trifecta[1][2]);
        $this->assertSame(19241, $trifecta[1][2][3]);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsEmptyVotesWhenNotOnSale(): void
    {
        $response = BoatcastScraperFactory::createVoteScraper()->scrape(
            '2026-08-01',
            22,
            1,
            MockBrowser::createSequence(array_fill(0, 6, [null, 403]))
        );

        $trifecta = $response['trifecta'];
        $totals = $response['totals'];

        $this->assertIsArray($trifecta);
        $this->assertSame(120, $this->countVotes($trifecta));
        $this->assertIsArray($trifecta[1]);
        $this->assertIsArray($trifecta[1][2]);
        $this->assertNull($trifecta[1][2][3]);

        $this->assertIsArray($totals);
        $this->assertIsArray($totals['trifecta']);
        $this->assertNull($totals['trifecta']['total']);
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\BoatcastScraper\Scrapers\VoteScraper::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        BoatcastScraperFactory::createVoteScraper()->ghost();
    }

    /**
     * @param mixed $votes
     * @return int
     */
    private function countVotes(mixed $votes): int
    {
        if (!is_array($votes)) {
            return 0;
        }

        $count = 0;

        /** @var mixed $value */
        foreach ($votes as $value) {
            $count += is_array($value) ? $this->countVotes($value) : 1;
        }

        return $count;
    }
}
