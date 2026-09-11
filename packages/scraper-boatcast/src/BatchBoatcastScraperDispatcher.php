<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper;

use Boatrace\Core\Concerns\IteratesBatchTargets;
use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperDispatcher as BatchBoatcastScraperDispatcherContract;
use Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperDispatcher as BoatcastScraperDispatcherContract;
use Boatrace\Types\Contracts\Progress\ProgressDispatcher as ProgressDispatcherContract;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class BatchBoatcastScraperDispatcher implements BatchBoatcastScraperDispatcherContract
{
    use IteratesBatchTargets;
    use RejectsUndefinedCalls;

    /**
     * @param \Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperDispatcher $boatcastScraper
     * @param \Boatrace\Types\Contracts\Progress\ProgressDispatcher $progress
     */
    public function __construct(
        private readonly BoatcastScraperDispatcherContract $boatcastScraper,
        private readonly ProgressDispatcherContract $progress,
    ) {
        //
    }

    /**
     * @return \Boatrace\Types\Contracts\Progress\ProgressDispatcher
     */
    #[\Override]
    protected function progress(): ProgressDispatcherContract
    {
        return $this->progress;
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return list<int<1, 24>>
     */
    #[\Override]
    protected function activeStadiumNumbers(DateTimeInterface|string $date, ?HttpBrowser $httpBrowser): array
    {
        return array_keys($this->boatcastScraper->scrapeStadium($date, $httpBrowser));
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    #[\Override]
    public function scrapeTime(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null
    ): array {
        return $this->iterateTargets(
            fn(DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber): array
                => $this->boatcastScraper->scrapeTime($date, $stadiumNumber, $raceNumber, $httpBrowser),
            'オリジナル展示データ',
            $date,
            $stadiumNumbers,
            $raceNumbers,
            $httpBrowser
        );
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @param bool $onSaleOnly
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    #[\Override]
    public function scrapeVote(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null,
        bool $onSaleOnly = false
    ): array {
        return $this->iterateTargets(
            fn(DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber): array
                => $this->boatcastScraper->scrapeVote($date, $stadiumNumber, $raceNumber, $httpBrowser, $onSaleOnly),
            '発売票数',
            $date,
            $stadiumNumbers,
            $raceNumbers,
            $httpBrowser
        );
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @param bool $onSaleOnly
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    #[\Override]
    public function scrapeOdds(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null,
        bool $onSaleOnly = false
    ): array {
        return $this->iterateTargets(
            fn(DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber): array
                => $this->boatcastScraper->scrapeOdds($date, $stadiumNumber, $raceNumber, $httpBrowser, $onSaleOnly),
            'オッズ',
            $date,
            $stadiumNumbers,
            $raceNumbers,
            $httpBrowser
        );
    }
}
