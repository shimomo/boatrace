<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\BatchScraper;

use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
interface BatchScraperDispatcher extends BatchScraper
{
    /**
     * @return bool
     */
    public function getShowProgress(): bool;

    /**
     * @param bool $showProgress
     * @return void
     */
    public function setShowProgress(bool $showProgress): void;

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    public function scrapeProgram(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null
    ): array;

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    public function scrapePreview(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null
    ): array;

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    public function scrapeOdds(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null
    ): array;

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    public function scrapeResult(
        DateTimeInterface|string $date,
        array $stadiumNumbers = [],
        array $raceNumbers = [],
        ?HttpBrowser $httpBrowser = null
    ): array;
}
