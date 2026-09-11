<?php

declare(strict_types=1);

namespace Boatrace\Scraper;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Scraper\Scraper as ScraperContract;
use Boatrace\Types\Contracts\Scraper\ScraperDispatcher as ScraperDispatcherContract;
use Boatrace\Types\Contracts\Scraper\ScraperResponse as ScraperResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     getStadiumNumbers()
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     getRaceNumbers()
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     getMinCallIntervalSeconds()
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     setMinCallIntervalSeconds(float $seconds)
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     throttle()
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     scrapeStadium(\DateTimeInterface|string $date, ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     scrapeProgram(\DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber, ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     scrapePreview(\DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber, ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     scrapeOdds(\DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber, ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\Scraper\ScraperResponse
 *     scrapeResult(\DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber, ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @author shimomo
 */
final class Scraper implements ScraperContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Scraper\ScraperDispatcher $scraper
     */
    public function __construct(private readonly ScraperDispatcherContract $scraper)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Scraper\ScraperResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): ScraperResponseContract
    {
        return self::toResponse(ScraperResponseContract::class, $this->scraper->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Scraper\ScraperResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): ScraperResponseContract
    {
        return self::assertResponse(
            ScraperResponseContract::class,
            CoreContainer::getInstance(ScraperContract::class)->$name(...$arguments),
        );
    }
}
