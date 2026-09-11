<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperDispatcher as BoatcastScraperDispatcherContract;
use Boatrace\Types\Contracts\BoatcastScraper\OddsScraper as OddsScraperContract;
use Boatrace\Types\Contracts\BoatcastScraper\StadiumScraper as StadiumScraperContract;
use Boatrace\Types\Contracts\BoatcastScraper\TimeScraper as TimeScraperContract;
use Boatrace\Types\Contracts\BoatcastScraper\VoteScraper as VoteScraperContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;
use Boatrace\Types\Contracts\Validator\ValidatorDispatcher as ValidatorDispatcherContract;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class BoatcastScraperDispatcher implements BoatcastScraperDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @param \Boatrace\Types\Contracts\BoatcastScraper\StadiumScraper $stadiumScraper
     * @param \Boatrace\Types\Contracts\BoatcastScraper\TimeScraper $timeScraper
     * @param \Boatrace\Types\Contracts\BoatcastScraper\VoteScraper $voteScraper
     * @param \Boatrace\Types\Contracts\BoatcastScraper\OddsScraper $oddsScraper
     * @param \Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher $throttler
     * @param \Boatrace\Types\Contracts\Validator\ValidatorDispatcher $validator
     */
    public function __construct(
        private readonly StadiumScraperContract $stadiumScraper,
        private readonly TimeScraperContract $timeScraper,
        private readonly VoteScraperContract $voteScraper,
        private readonly OddsScraperContract $oddsScraper,
        private readonly ThrottlerDispatcherContract $throttler,
        private readonly ValidatorDispatcherContract $validator,
    ) {
        //
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
        $this->throttler->throttle();

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
    public function scrapeTime(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null
    ): array {
        $this->validator->between($stadiumNumber, 1, 24, '$stadiumNumber');
        $this->validator->between($raceNumber, 1, 12, '$raceNumber');

        $this->throttler->throttle();

        return $this->timeScraper->scrape($date, $stadiumNumber, $raceNumber, $httpBrowser);
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
    public function scrapeVote(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null,
        bool $onSaleOnly = false
    ): array {
        $this->validator->between($stadiumNumber, 1, 24, '$stadiumNumber');
        $this->validator->between($raceNumber, 1, 12, '$raceNumber');

        $this->throttler->throttle();

        return $this->voteScraper->scrape($date, $stadiumNumber, $raceNumber, $httpBrowser, $onSaleOnly);
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
    public function scrapeOdds(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null,
        bool $onSaleOnly = false
    ): array {
        $this->validator->between($stadiumNumber, 1, 24, '$stadiumNumber');
        $this->validator->between($raceNumber, 1, 12, '$raceNumber');

        $this->throttler->throttle();

        return $this->oddsScraper->scrape($date, $stadiumNumber, $raceNumber, $httpBrowser, $onSaleOnly);
    }
}
