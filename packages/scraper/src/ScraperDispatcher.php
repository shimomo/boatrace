<?php

declare(strict_types=1);

namespace Boatrace\Scraper;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Scraper\OddsScraper as OddsScraperContract;
use Boatrace\Types\Contracts\Scraper\PreviewScraper as PreviewScraperContract;
use Boatrace\Types\Contracts\Scraper\ProgramScraper as ProgramScraperContract;
use Boatrace\Types\Contracts\Scraper\ResultScraper as ResultScraperContract;
use Boatrace\Types\Contracts\Scraper\ScraperDispatcher as ScraperDispatcherContract;
use Boatrace\Types\Contracts\Scraper\StadiumScraper as StadiumScraperContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;
use Boatrace\Types\Contracts\Validator\ValidatorDispatcher as ValidatorDispatcherContract;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class ScraperDispatcher implements ScraperDispatcherContract
{
    use RejectsUndefinedCalls;

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
     * @param \Boatrace\Types\Contracts\Scraper\StadiumScraper $stadiumScraper
     * @param \Boatrace\Types\Contracts\Scraper\ProgramScraper $programScraper
     * @param \Boatrace\Types\Contracts\Scraper\PreviewScraper $previewScraper
     * @param \Boatrace\Types\Contracts\Scraper\OddsScraper $oddsScraper
     * @param \Boatrace\Types\Contracts\Scraper\ResultScraper $resultScraper
     * @param \Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher $throttler
     * @param \Boatrace\Types\Contracts\Validator\ValidatorDispatcher $validator
     */
    public function __construct(
        private readonly StadiumScraperContract $stadiumScraper,
        private readonly ProgramScraperContract $programScraper,
        private readonly PreviewScraperContract $previewScraper,
        private readonly OddsScraperContract $oddsScraper,
        private readonly ResultScraperContract $resultScraper,
        private readonly ThrottlerDispatcherContract $throttler,
        private readonly ValidatorDispatcherContract $validator,
    ) {
        //
    }

    /**
     * @return non-empty-list<int<1, 24>>
     */
    #[\Override]
    public function getStadiumNumbers(): array
    {
        return self::STADIUM_NUMBERS;
    }

    /**
     * @return non-empty-list<int<1, 12>>
     */
    #[\Override]
    public function getRaceNumbers(): array
    {
        return self::RACE_NUMBERS;
    }

    /**
     * @return float
     */
    #[\Override]
    public function getMinCallIntervalSeconds(): float
    {
        return $this->throttler->getMinCallIntervalSeconds();
    }

    /**
     * @param float $seconds
     * @return void
     * @throws \ValueError
     */
    #[\Override]
    public function setMinCallIntervalSeconds(float $seconds): void
    {
        $this->throttler->setMinCallIntervalSeconds($seconds);
    }

    /**
     * @return void
     */
    #[\Override]
    public function throttle(): void
    {
        $this->throttler->throttle();
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, non-empty-string>
     */
    #[\Override]
    public function scrapeStadium(
        DateTimeInterface|string $date,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $this->throttle();

        return $this->stadiumScraper->scrape($date, $httpBrowser);
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeProgram(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $this->validate($stadiumNumber, $raceNumber);
        $this->throttle();

        return $this->programScraper->scrape($date, $stadiumNumber, $raceNumber, $httpBrowser);
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapePreview(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $this->validate($stadiumNumber, $raceNumber);
        $this->throttle();

        return $this->previewScraper->scrape($date, $stadiumNumber, $raceNumber, $httpBrowser);
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeOdds(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $this->validate($stadiumNumber, $raceNumber);
        $this->throttle();

        return $this->oddsScraper->scrape($date, $stadiumNumber, $raceNumber, $httpBrowser);
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrapeResult(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $this->validate($stadiumNumber, $raceNumber);
        $this->throttle();

        return $this->resultScraper->scrape($date, $stadiumNumber, $raceNumber, $httpBrowser);
    }

    /**
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return void
     * @throws \ValueError
     */
    private function validate(int $stadiumNumber, int $raceNumber): void
    {
        $this->validator->between($stadiumNumber, 1, 24, '$stadiumNumber');
        $this->validator->between($raceNumber, 1, 12, '$raceNumber');
    }
}
