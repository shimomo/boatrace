<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Scrapers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Filter\FilterDispatcher as FilterDispatcherContract;
use Boatrace\Types\Contracts\Filter\OddsFilter as OddsFilterContract;
use Boatrace\Types\Contracts\Scraper\OddsScraper as OddsScraperContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;
use Carbon\CarbonImmutable as Carbon;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class OddsScraper implements OddsScraperContract
{
    use RejectsUndefinedCalls;

    /**
     * @var non-empty-string
     */
    private const string BASE_URL = 'https://www.boatrace.jp';

    /**
     * @var non-empty-string
     */
    private const string BASE_XPATH = 'descendant-or-self::body/main/div/div/div';

    /**
     * @param \Boatrace\Types\Contracts\Browser\BrowserDispatcher $browser
     * @param \Boatrace\Types\Contracts\Filter\FilterDispatcher $filter
     * @param \Boatrace\Types\Contracts\Filter\OddsFilter $oddsFilter
     * @param \Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher $throttler
     */
    public function __construct(
        private readonly BrowserDispatcherContract $browser,
        private readonly FilterDispatcherContract $filter,
        private readonly OddsFilterContract $oddsFilter,
        private readonly ThrottlerDispatcherContract $throttler,
    ) {
        //
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrape(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null,
    ): array {
        $date = Carbon::parse($date);

        $response = [];

        $response += $this->scrapeTrifecta($date, $stadiumNumber, $raceNumber, $httpBrowser);
        $this->throttler->throttle();
        $response += $this->scrapeTrio($date, $stadiumNumber, $raceNumber, $httpBrowser);
        $this->throttler->throttle();
        $response += $this->scrapeExactaAndQuinella($date, $stadiumNumber, $raceNumber, $httpBrowser);
        $this->throttler->throttle();
        $response += $this->scrapeQuinellaPlace($date, $stadiumNumber, $raceNumber, $httpBrowser);
        $this->throttler->throttle();
        $response += $this->scrapeWinAndPlace($date, $stadiumNumber, $raceNumber, $httpBrowser);

        return $response;
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeTrifecta(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $date = Carbon::parse($date);

        $scraperFormat = '%s/owpc/pc/race/odds3t?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, self::BASE_URL, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = ($httpBrowser ?? $this->browser->create())->request('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, self::BASE_XPATH);

        $baseLevel = 0;
        if ($this->filter->byXPath($scraper, $levelXPath) !== null) {
            $baseLevel = 1;
        }

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        foreach (range(1, 6) as $first) {
            $seconds = self::entryNumbersExcept($first);

            foreach ($seconds as $secondIndex => $second) {
                $thirds = self::entryNumbersExcept($first, $second);

                foreach ($thirds as $thirdIndex => $third) {
                    $response['trifecta'][$first][$second][$third] = $this->oddsFilter->byXPath(
                        $scraper,
                        self::cellXPath(
                            $baseLevel + 7,
                            $secondIndex * 4 + $thirdIndex + 1,
                            $thirdIndex === 0 ? 3 * $first : 2 * $first
                        )
                    );
                }
            }
        }

        return $response;
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeTrio(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $date = Carbon::parse($date);

        $scraperFormat = '%s/owpc/pc/race/odds3f?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, self::BASE_URL, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = ($httpBrowser ?? $this->browser->create())->request('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, self::BASE_XPATH);

        $baseLevel = 0;
        if ($this->filter->byXPath($scraper, $levelXPath) !== null) {
            $baseLevel = 1;
        }

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        foreach (self::ascendingTriples() as [$first, $second, $third]) {
            $response['trio'][$first][$second][$third] = $this->oddsFilter->byXPath(
                $scraper,
                self::cellXPath(
                    $baseLevel + 7,
                    self::ascendingPairRow($second, $third),
                    $third === $second + 1 ? 3 * $first : 2 * $first
                )
            );
        }

        return $response;
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeExactaAndQuinella(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $date = Carbon::parse($date);

        $scraperFormat = '%s/owpc/pc/race/odds2tf?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, self::BASE_URL, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = ($httpBrowser ?? $this->browser->create())->request('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, self::BASE_XPATH);

        $baseLevel = 0;
        if ($this->filter->byXPath($scraper, $levelXPath) !== null) {
            $baseLevel = 1;
        }

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        foreach (range(1, 6) as $first) {
            foreach (self::entryNumbersExcept($first) as $secondIndex => $second) {
                $response['exacta'][$first][$second] = $this->oddsFilter->byXPath(
                    $scraper,
                    self::cellXPath($baseLevel + 7, $secondIndex + 1, 2 * $first)
                );
            }
        }

        foreach (self::ascendingPairs() as [$first, $second]) {
            $response['quinella'][$first][$second] = $this->oddsFilter->byXPath(
                $scraper,
                self::cellXPath($baseLevel + 9, $second - 1, 2 * $first)
            );
        }

        return $response;
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeExacta(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        return $this->scrapeExactaAndQuinella($date, $stadiumNumber, $raceNumber, $httpBrowser);
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeQuinella(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        return $this->scrapeExactaAndQuinella($date, $stadiumNumber, $raceNumber, $httpBrowser);
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeQuinellaPlace(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $date = Carbon::parse($date);

        $scraperFormat = '%s/owpc/pc/race/oddsk?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, self::BASE_URL, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = ($httpBrowser ?? $this->browser->create())->request('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, self::BASE_XPATH);

        $baseLevel = 0;
        if ($this->filter->byXPath($scraper, $levelXPath) !== null) {
            $baseLevel = 1;
        }

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        foreach (self::ascendingPairs() as [$first, $second]) {
            $response['quinella_place'][$first][$second] = $this->oddsFilter->byXPathAsRange(
                $scraper,
                self::cellXPath($baseLevel + 7, $second - 1, 2 * $first)
            );
        }

        return $response;
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeWinAndPlace(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $date = Carbon::parse($date);

        $scraperFormat = '%s/owpc/pc/race/oddstf?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, self::BASE_URL, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = ($httpBrowser ?? $this->browser->create())->request('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, self::BASE_XPATH);

        $baseLevel = 0;
        if ($this->filter->byXPath($scraper, $levelXPath) !== null) {
            $baseLevel = 1;
        }

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        foreach (range(1, 6) as $entryNumber) {
            $response['win'][$entryNumber] = $this->oddsFilter->byXPath(
                $scraper,
                self::singleCellXPath($baseLevel + 6, 1, $entryNumber)
            );
        }

        foreach (range(1, 6) as $entryNumber) {
            $response['place'][$entryNumber] = $this->oddsFilter->byXPathAsRange(
                $scraper,
                self::singleCellXPath($baseLevel + 6, 2, $entryNumber)
            );
        }

        return $response;
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeWin(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        return $this->scrapeWinAndPlace($date, $stadiumNumber, $raceNumber, $httpBrowser);
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapePlace(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        return $this->scrapeWinAndPlace($date, $stadiumNumber, $raceNumber, $httpBrowser);
    }

    /**
     * @param int ...$excluded
     * @return list<int>
     */
    private static function entryNumbersExcept(int ...$excluded): array
    {
        return array_values(array_diff(range(1, 6), $excluded));
    }

    /**
     * @return non-empty-list<array{int, int}>
     */
    private static function ascendingPairs(): array
    {
        $pairs = [];

        foreach (range(1, 5) as $first) {
            foreach (range($first + 1, 6) as $second) {
                $pairs[] = [$first, $second];
            }
        }

        return $pairs;
    }

    /**
     * @return non-empty-list<array{int, int, int}>
     */
    private static function ascendingTriples(): array
    {
        $triples = [];

        foreach (range(1, 4) as $first) {
            foreach (range($first + 1, 5) as $second) {
                foreach (range($second + 1, 6) as $third) {
                    $triples[] = [$first, $second, $third];
                }
            }
        }

        return $triples;
    }

    /**
     * @param int $second
     * @param int $third
     * @return int
     */
    private static function ascendingPairRow(int $second, int $third): int
    {
        $row = 1;

        foreach (range(2, 5) as $first) {
            foreach (range($first + 1, 6) as $last) {
                if ($first === $second && $last === $third) {
                    return $row;
                }

                $row++;
            }
        }

        return $row;
    }

    /**
     * @param int $level
     * @param int $row
     * @param int $column
     * @return non-empty-string
     */
    private static function cellXPath(int $level, int $row, int $column): string
    {
        return sprintf(
            '%s/div[2]/div[%d]/table/tbody/tr[%d]/td[%d]',
            self::BASE_XPATH,
            $level,
            $row,
            $column
        );
    }

    /**
     * @param int $level
     * @param int $block
     * @param int $entryNumber
     * @return non-empty-string
     */
    private static function singleCellXPath(int $level, int $block, int $entryNumber): string
    {
        return sprintf(
            '%s/div[2]/div[%d]/div[%d]/div[2]/table/tbody[%d]/tr/td[3]',
            self::BASE_XPATH,
            $level,
            $block,
            $entryNumber
        );
    }
}
