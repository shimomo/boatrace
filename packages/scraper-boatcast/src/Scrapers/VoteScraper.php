<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Scrapers;

use Boatrace\Types\Contracts\BoatcastScraper\VoteScraper as VoteScraperContract;
use Carbon\CarbonImmutable as Carbon;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class VoteScraper extends BoatcastFileScraper implements VoteScraperContract
{
    /**
     * @return non-empty-string
     */
    #[\Override]
    protected function fileName(): string
    {
        return 'hyousu';
    }

    /**
     * @return non-empty-string
     */
    #[\Override]
    protected function onSalePrefix(): string
    {
        return 'api';
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
        $response['quinella'] = $this->scrapePairs($twoWay[9] ?? []);
        $response['quinella_place'] = $this->scrapePairs($other[4] ?? []);
        $response['win'] = $this->scrapeSingles($other[6] ?? []);
        $response['place'] = $this->scrapeSingles($other[8] ?? []);

        $response['totals'] = [
            'trifecta' => $this->scrapeTotals($threeWay[8] ?? []),
            'trio' => $this->scrapeTotals($other[3] ?? []),
            'exacta' => $this->scrapeTotals($twoWay[8] ?? []),
            'quinella' => $this->scrapeTotals($twoWay[10] ?? []),
            'quinella_place' => $this->scrapeTotals($other[5] ?? []),
            'win' => $this->scrapeTotals($other[7] ?? []),
            'place' => $this->scrapeTotals($other[9] ?? []),
        ];

        return $response;
    }

    /**
     * @param list<list<string>> $rows
     * @return array<int, array<int, array<int, ?int>>>
     */
    private function scrapeTrifecta(array $rows): array
    {
        $response = [];

        foreach (range(1, 6) as $first) {
            $votes = $rows[$first + 1] ?? [];
            $index = 1;

            foreach (range(1, 6) as $second) {
                foreach (range(1, 6) as $third) {
                    if ($second === $third || $second === $first || $third === $first) {
                        continue;
                    }

                    $response[$first][$second][$third] = $this->toVotes($votes[$index] ?? null);
                    $index++;
                }
            }
        }

        return $response;
    }

    /**
     * @param list<string> $votes
     * @return array<int, array<int, array<int, ?int>>>
     */
    private function scrapeTrio(array $votes): array
    {
        $response = [];
        $index = 0;

        foreach (range(1, 4) as $first) {
            foreach (range($first + 1, 5) as $second) {
                foreach (range($second + 1, 6) as $third) {
                    $response[$first][$second][$third] = $this->toVotes($votes[$index] ?? null);
                    $index++;
                }
            }
        }

        return $response;
    }

    /**
     * @param list<list<string>> $rows
     * @return array<int, array<int, ?int>>
     */
    private function scrapeExacta(array $rows): array
    {
        $response = [];

        foreach (range(1, 6) as $first) {
            $votes = $rows[$first + 1] ?? [];
            $index = 1;

            foreach (range(1, 6) as $second) {
                if ($second === $first) {
                    continue;
                }

                $response[$first][$second] = $this->toVotes($votes[$index] ?? null);
                $index++;
            }
        }

        return $response;
    }

    /**
     * @param list<string> $votes
     * @return array<int, array<int, ?int>>
     */
    private function scrapePairs(array $votes): array
    {
        $response = [];
        $index = 0;

        foreach (range(1, 5) as $first) {
            foreach (range($first + 1, 6) as $second) {
                $response[$first][$second] = $this->toVotes($votes[$index] ?? null);
                $index++;
            }
        }

        return $response;
    }

    /**
     * @param list<string> $votes
     * @return array<int, ?int>
     */
    private function scrapeSingles(array $votes): array
    {
        $response = [];

        foreach (range(1, 6) as $entryNumber) {
            $response[$entryNumber] = $this->toVotes($votes[$entryNumber - 1] ?? null);
        }

        return $response;
    }

    /**
     * @param list<string> $totals
     * @return array{
     *     total: ?int,
     *     refunded: ?int,
     *     effective: ?int,
     * }
     */
    private function scrapeTotals(array $totals): array
    {
        return [
            'total' => $this->toVotes($totals[0] ?? null),
            'refunded' => $this->toVotes($totals[1] ?? null),
            'effective' => $this->toVotes($totals[2] ?? null),
        ];
    }

    /**
     * @param ?string $value
     * @return ?int
     */
    private function toVotes(?string $value): ?int
    {
        return is_numeric($value) ? $this->converter->toInt($value) : null;
    }
}
