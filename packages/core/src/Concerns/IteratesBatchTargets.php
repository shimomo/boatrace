<?php

declare(strict_types=1);

namespace Boatrace\Core\Concerns;

use Boatrace\Types\Contracts\Progress\ProgressDispatcher as ProgressDispatcherContract;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
trait IteratesBatchTargets
{
    /**
     * @var non-empty-list<int<1, 24>>
     */
    private const array STADIUM_NUMBERS = [
        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
        13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24,
    ];

    /**
     * @var non-empty-list<int<1, 12>>
     */
    private const array RACE_NUMBERS = [
        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
    ];

    /**
     * @return \Boatrace\Types\Contracts\Progress\ProgressDispatcher
     */
    abstract protected function progress(): ProgressDispatcherContract;

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return list<int<1, 24>>
     */
    abstract protected function activeStadiumNumbers(
        DateTimeInterface|string $date,
        ?HttpBrowser $httpBrowser
    ): array;

    /**
     * @return bool
     */
    public function getShowProgress(): bool
    {
        return $this->progress()->isEnabled();
    }

    /**
     * @param bool $showProgress
     * @return void
     */
    public function setShowProgress(bool $showProgress): void
    {
        $this->progress()->setEnabled($showProgress);
    }

    /**
     * @param callable(\DateTimeInterface|non-empty-string, int<1, 24>, int<1, 12>): array<non-empty-string, mixed> $scraper
     * @param non-empty-string $label
     * @param \DateTimeInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    protected function iterateTargets(
        callable $scraper,
        string $label,
        DateTimeInterface|string $date,
        array $stadiumNumbers,
        array $raceNumbers,
        ?HttpBrowser $httpBrowser
    ): array {
        $response = [];

        $uniqueStadiumNumbers = array_unique($stadiumNumbers ?: self::STADIUM_NUMBERS);
        $uniqueRaceNumbers = array_unique($raceNumbers ?: self::RACE_NUMBERS);

        $activeStadiumNumbers = array_intersect(
            $uniqueStadiumNumbers,
            $this->activeStadiumNumbers($date, $httpBrowser)
        );

        $totalSteps = count($activeStadiumNumbers) * count($uniqueRaceNumbers);

        $this->progress()->start($totalSteps, sprintf('%sのスクレイピングを開始します', $label));

        foreach ($activeStadiumNumbers as $stadiumNumber) {
            foreach ($uniqueRaceNumbers as $raceNumber) {
                $response[$stadiumNumber][$raceNumber] = $scraper($date, $stadiumNumber, $raceNumber);

                $this->progress()->advance();
            }
        }

        $this->progress()->finish(
            sprintf('%sのスクレイピングが完了しました（%d件）', $label, $totalSteps)
        );

        return $response;
    }
}
