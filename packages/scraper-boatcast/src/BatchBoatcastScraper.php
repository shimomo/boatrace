<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraper as BatchBoatcastScraperContract;
use Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperDispatcher as BatchBoatcastScraperDispatcherContract;
use Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse as BatchBoatcastScraperResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse
 *     getShowProgress()
 * @method static \Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse
 *     setShowProgress(bool $showProgress)
 * @method static \Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse
 *     scrapeTime(\DateTimeInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = [], ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse
 *     scrapeVote(\DateTimeInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = [], ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse
 *     scrapeOdds(\DateTimeInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = [], ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @author shimomo
 */
final class BatchBoatcastScraper implements BatchBoatcastScraperContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperDispatcher $batchBoatcastScraper
     */
    public function __construct(private readonly BatchBoatcastScraperDispatcherContract $batchBoatcastScraper)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): BatchBoatcastScraperResponseContract
    {
        return self::toResponse(BatchBoatcastScraperResponseContract::class, $this->batchBoatcastScraper->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): BatchBoatcastScraperResponseContract
    {
        return self::assertResponse(
            BatchBoatcastScraperResponseContract::class,
            CoreContainer::getInstance(BatchBoatcastScraperContract::class)->$name(...$arguments),
        );
    }
}
