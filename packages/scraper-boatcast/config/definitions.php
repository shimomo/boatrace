<?php

declare(strict_types=1);

use Boatrace\BoatcastScraper\BatchBoatcastScraper;
use Boatrace\BoatcastScraper\BatchBoatcastScraperDispatcher;
use Boatrace\BoatcastScraper\BatchBoatcastScraperResponse;
use Boatrace\BoatcastScraper\BoatcastScraper;
use Boatrace\BoatcastScraper\BoatcastScraperDispatcher;
use Boatrace\BoatcastScraper\BoatcastScraperResponse;
use Boatrace\BoatcastScraper\Scrapers\OddsScraper;
use Boatrace\BoatcastScraper\Scrapers\StadiumScraper;
use Boatrace\BoatcastScraper\Scrapers\TimeScraper;
use Boatrace\BoatcastScraper\Scrapers\VoteScraper;
use Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraper as BatchBoatcastScraperContract;
use Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperDispatcher as BatchBoatcastScraperDispatcherContract;
use Boatrace\Types\Contracts\BatchBoatcastScraper\BatchBoatcastScraperResponse as BatchBoatcastScraperResponseContract;
use Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraper as BoatcastScraperContract;
use Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperDispatcher as BoatcastScraperDispatcherContract;
use Boatrace\Types\Contracts\BoatcastScraper\BoatcastScraperResponse as BoatcastScraperResponseContract;
use Boatrace\Types\Contracts\BoatcastScraper\OddsScraper as OddsScraperContract;
use Boatrace\Types\Contracts\BoatcastScraper\StadiumScraper as StadiumScraperContract;
use Boatrace\Types\Contracts\BoatcastScraper\TimeScraper as TimeScraperContract;
use Boatrace\Types\Contracts\BoatcastScraper\VoteScraper as VoteScraperContract;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Json\JsonDispatcher as JsonDispatcherContract;
use Boatrace\Types\Contracts\Normalizer\NormalizerDispatcher as NormalizerDispatcherContract;
use Boatrace\Types\Contracts\Progress\ProgressDispatcher as ProgressDispatcherContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;
use Boatrace\Types\Contracts\Tsv\TsvDispatcher as TsvDispatcherContract;
use Boatrace\Types\Contracts\Validator\ValidatorDispatcher as ValidatorDispatcherContract;

return [
    BatchBoatcastScraperContract::class => \DI\autowire(BatchBoatcastScraper::class)
        ->constructor(\DI\get(BatchBoatcastScraperDispatcherContract::class)),
    BatchBoatcastScraperDispatcherContract::class => \DI\autowire(BatchBoatcastScraperDispatcher::class)
        ->constructor(
            \DI\get(BoatcastScraperDispatcherContract::class),
            \DI\get(ProgressDispatcherContract::class),
        ),
    BatchBoatcastScraperResponseContract::class => function (
        \DI\Container $container,
        bool|array|null $value = null
    ) {
        return new BatchBoatcastScraperResponse($value);
    },
    BoatcastScraperContract::class => \DI\autowire(BoatcastScraper::class)
        ->constructor(\DI\get(BoatcastScraperDispatcherContract::class)),
    BoatcastScraperDispatcherContract::class => \DI\autowire(BoatcastScraperDispatcher::class)
        ->constructor(
            \DI\get(StadiumScraperContract::class),
            \DI\get(TimeScraperContract::class),
            \DI\get(VoteScraperContract::class),
            \DI\get(OddsScraperContract::class),
            \DI\get(ThrottlerDispatcherContract::class),
            \DI\get(ValidatorDispatcherContract::class),
        ),
    BoatcastScraperResponseContract::class => function (\DI\Container $container, ?array $value = null) {
        return new BoatcastScraperResponse($value);
    },
    StadiumScraperContract::class => \DI\autowire(StadiumScraper::class)
        ->constructor(
            \DI\get(BrowserDispatcherContract::class),
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(JsonDispatcherContract::class),
        ),
    TimeScraperContract::class => \DI\autowire(TimeScraper::class)
        ->constructor(
            \DI\get(BrowserDispatcherContract::class),
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(NormalizerDispatcherContract::class),
            \DI\get(TsvDispatcherContract::class),
        ),
    VoteScraperContract::class => \DI\autowire(VoteScraper::class)
        ->constructor(
            \DI\get(BrowserDispatcherContract::class),
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(ThrottlerDispatcherContract::class),
            \DI\get(TsvDispatcherContract::class),
        ),
    OddsScraperContract::class => \DI\autowire(OddsScraper::class)
        ->constructor(
            \DI\get(BrowserDispatcherContract::class),
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(ThrottlerDispatcherContract::class),
            \DI\get(TsvDispatcherContract::class),
        ),
];
