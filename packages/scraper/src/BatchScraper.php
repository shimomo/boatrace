<?php

declare(strict_types=1);

namespace Boatrace\Scraper;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\BatchScraper\BatchScraper as BatchScraperContract;
use Boatrace\Types\Contracts\BatchScraper\BatchScraperDispatcher as BatchScraperDispatcherContract;
use Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse as BatchScraperResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse
 *     getShowProgress()
 * @method static \Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse
 *     setShowProgress(bool $showProgress)
 * @method static \Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse
 *     scrapeProgram(\DateTimeInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = [], ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse
 *     scrapePreview(\DateTimeInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = [], ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse
 *     scrapeOdds(\DateTimeInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = [], ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse
 *     scrapeResult(\DateTimeInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = [], ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @author shimomo
 */
final class BatchScraper implements BatchScraperContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\BatchScraper\BatchScraperDispatcher $batchScraper
     */
    public function __construct(private readonly BatchScraperDispatcherContract $batchScraper)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): BatchScraperResponseContract
    {
        return self::toResponse(BatchScraperResponseContract::class, $this->batchScraper->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): BatchScraperResponseContract
    {
        return self::assertResponse(
            BatchScraperResponseContract::class,
            CoreContainer::getInstance(BatchScraperContract::class)->$name(...$arguments),
        );
    }
}
