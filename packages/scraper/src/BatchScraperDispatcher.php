<?php

declare(strict_types=1);

namespace Boatrace\Scraper;

use Boatrace\Core\Concerns\IteratesBatchTargets;
use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\BatchScraper\BatchScraperDispatcher as BatchScraperDispatcherContract;
use Boatrace\Types\Contracts\Progress\ProgressDispatcher as ProgressDispatcherContract;
use Boatrace\Types\Contracts\Scraper\ScraperDispatcher as ScraperDispatcherContract;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class BatchScraperDispatcher implements BatchScraperDispatcherContract
{
    use IteratesBatchTargets;
    use RejectsUndefinedCalls;

    /**
     * @param \Boatrace\Types\Contracts\Scraper\ScraperDispatcher $scraper
     * @param \Boatrace\Types\Contracts\Progress\ProgressDispatcher $progress
     */
    public function __construct(
        private readonly ScraperDispatcherContract $scraper,
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
        return array_keys($this->scraper->scrapeStadium($date, $httpBrowser));
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    #[\Override]
    public function scrapeProgram(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null
    ): array {
        return $this->iterateTargets(
            fn(DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber): array
                => $this->scraper->scrapeProgram($date, $stadiumNumber, $raceNumber, $httpBrowser),
            '出走表',
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
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    #[\Override]
    public function scrapePreview(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null
    ): array {
        return $this->iterateTargets(
            fn(DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber): array
                => $this->scraper->scrapePreview($date, $stadiumNumber, $raceNumber, $httpBrowser),
            '直前情報',
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
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    #[\Override]
    public function scrapeOdds(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null
    ): array {
        return $this->iterateTargets(
            fn(DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber): array
                => $this->scraper->scrapeOdds($date, $stadiumNumber, $raceNumber, $httpBrowser),
            'オッズ',
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
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    #[\Override]
    public function scrapeResult(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null
    ): array {
        return $this->iterateTargets(
            fn(DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber): array
                => $this->scraper->scrapeResult($date, $stadiumNumber, $raceNumber, $httpBrowser),
            '結果',
            $date,
            $stadiumNumbers,
            $raceNumbers,
            $httpBrowser
        );
    }
}
