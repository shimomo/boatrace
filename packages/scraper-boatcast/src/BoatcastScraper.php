<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraper as BoatcastScraperContract;
use Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperDispatcher as BoatcastScraperDispatcherContract;
use Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperResponse as BoatcastScraperResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperResponse
 *     scrapeStadium(\DateTimeInterface|string $date, ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperResponse
 *     scrapeTime(\DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber, ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null)
 * @method static \Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperResponse
 *     scrapeVote(\DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber, ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null, bool $onSaleOnly = false)
 * @method static \Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperResponse
 *     scrapeOdds(\DateTimeInterface|string $date, int $stadiumNumber, int $raceNumber, ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser = null, bool $onSaleOnly = false)
 * @author shimomo
 */
final class BoatcastScraper implements BoatcastScraperContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperDispatcher $boatcastScraper
     */
    public function __construct(private readonly BoatcastScraperDispatcherContract $boatcastScraper)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): BoatcastScraperResponseContract
    {
        return self::toResponse(BoatcastScraperResponseContract::class, $this->boatcastScraper->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): BoatcastScraperResponseContract
    {
        return self::assertResponse(
            BoatcastScraperResponseContract::class,
            CoreContainer::getInstance(BoatcastScraperContract::class)->$name(...$arguments),
        );
    }
}
