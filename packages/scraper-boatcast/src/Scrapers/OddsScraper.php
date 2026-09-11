<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Scrapers;

use Boatrace\Types\Contracts\BoatcastScraper\OddsScraper as OddsScraperContract;
use Carbon\CarbonImmutable as Carbon;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class OddsScraper extends BoatcastFileScraper implements OddsScraperContract
{
    /**
     * @return non-empty-string
     */
    #[\Override]
    protected function fileName(): string
    {
        return 'od';
    }

    /**
     * @return non-empty-string
     */
    #[\Override]
    protected function onSalePrefix(): string
    {
        return 'smt';
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @param bool $onSaleOnly
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrape(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null,
        bool $onSaleOnly = false,
    ): array {
        $date = Carbon::parse($date);

        [$other, $otherSource] = $this->request($date, $stadiumNumber, $raceNumber, 1, $httpBrowser, $onSaleOnly);
        $this->throttler->throttle();
        [$twoWay, $twoWaySource] = $this->request($date, $stadiumNumber, $raceNumber, 2, $httpBrowser, $onSaleOnly);
        $this->throttler->throttle();
        [$threeWay, $threeWaySource] = $this->request($date, $stadiumNumber, $raceNumber, 3, $httpBrowser, $onSaleOnly);

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;
        $response['is_fixed'] = $otherSource['is_fixed'] && $twoWaySource['is_fixed'] && $threeWaySource['is_fixed'];

        $response['sources'] = [
            1 => $otherSource,
            2 => $twoWaySource,
            3 => $threeWaySource,
        ];

        $response['trifecta'] = $this->scrapeTrifecta($threeWay);
        $response['trio'] = $this->scrapeTrio($other[2] ?? []);
        $response['exacta'] = $this->scrapeExacta($twoWay);
        $response['quinella'] = $this->scrapePairs($twoWay[8] ?? []);
        $response['quinella_place'] = $this->scrapeRangedPairs($other[3] ?? []);
        $response['win'] = $this->scrapeSingles($other[4] ?? []);
        $response['place'] = $this->scrapeRangedSingles($other[7] ?? []);

        return $response;
    }

    /**
     * @param list<list<string>> $rows
     * @return array<int, array<int, array<int, ?float>>>
     */
    private function scrapeTrifecta(array $rows): array
    {
        $response = [];

        foreach (range(1, 6) as $first) {
            $odds = $rows[$first + 1] ?? [];
            $index = 1;

            foreach (range(1, 6) as $second) {
                foreach (range(1, 6) as $third) {
                    if ($second === $third || $second === $first || $third === $first) {
                        continue;
                    }

                    $response[$first][$second][$third] = $this->toOdds($odds[$index] ?? null);
                    $index++;
                }
            }
        }

        return $response;
    }

    /**
     * @param list<string> $odds
     * @return array<int, array<int, array<int, ?float>>>
     */
    private function scrapeTrio(array $odds): array
    {
        $response = [];
        $index = 0;

        foreach (range(1, 4) as $first) {
            foreach (range($first + 1, 5) as $second) {
                foreach (range($second + 1, 6) as $third) {
                    $response[$first][$second][$third] = $this->toOdds($odds[$index] ?? null);
                    $index++;
                }
            }
        }

        return $response;
    }

    /**
     * @param list<list<string>> $rows
     * @return array<int, array<int, ?float>>
     */
    private function scrapeExacta(array $rows): array
    {
        $response = [];

        foreach (range(1, 6) as $first) {
            $odds = $rows[$first + 1] ?? [];
            $index = 1;

            foreach (range(1, 6) as $second) {
                if ($second === $first) {
                    continue;
                }

                $response[$first][$second] = $this->toOdds($odds[$index] ?? null);
                $index++;
            }
        }

        return $response;
    }

    /**
     * @param list<string> $odds
     * @return array<int, array<int, ?float>>
     */
    private function scrapePairs(array $odds): array
    {
        $response = [];
        $index = 0;

        foreach (range(1, 5) as $first) {
            foreach (range($first + 1, 6) as $second) {
                $response[$first][$second] = $this->toOdds($odds[$index] ?? null);
                $index++;
            }
        }

        return $response;
    }

    /**
     * @param list<string> $odds
     * @return array<int, array<int, array{lower_limit: ?float, upper_limit: ?float}>>
     */
    private function scrapeRangedPairs(array $odds): array
    {
        $response = [];
        $index = 0;

        foreach (range(1, 5) as $first) {
            foreach (range($first + 1, 6) as $second) {
                $response[$first][$second] = $this->toRangedOdds($odds, $index);
                $index += 3;
            }
        }

        return $response;
    }

    /**
     * @param list<string> $odds
     * @return array<int, ?float>
     */
    private function scrapeSingles(array $odds): array
    {
        $response = [];

        foreach (range(1, 6) as $entryNumber) {
            $response[$entryNumber] = $this->toOdds($odds[$entryNumber - 1] ?? null);
        }

        return $response;
    }

    /**
     * @param list<string> $odds
     * @return array<int, array{lower_limit: ?float, upper_limit: ?float}>
     */
    private function scrapeRangedSingles(array $odds): array
    {
        $response = [];
        $index = 0;

        foreach (range(1, 6) as $entryNumber) {
            $response[$entryNumber] = $this->toRangedOdds($odds, $index);
            $index += 3;
        }

        return $response;
    }

    /**
     * @param list<string> $odds
     * @param int $index
     * @return array{lower_limit: ?float, upper_limit: ?float}
     */
    private function toRangedOdds(array $odds, int $index): array
    {
        return [
            'lower_limit' => $this->toOdds($odds[$index] ?? null),
            'upper_limit' => $this->toOdds($odds[$index + 2] ?? null),
        ];
    }

    /**
     * @param ?string $value
     * @return ?float
     */
    private function toOdds(?string $value): ?float
    {
        if (!is_numeric($value)) {
            return null;
        }

        $odds = $this->converter->toFloat($value);

        return $odds !== null && $odds >= 1.0 ? $odds : null;
    }
}
