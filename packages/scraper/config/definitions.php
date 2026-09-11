<?php

declare(strict_types=1);

use Boatrace\Scraper\BatchScraper;
use Boatrace\Scraper\BatchScraperDispatcher;
use Boatrace\Scraper\BatchScraperResponse;
use Boatrace\Scraper\Filters\OddsFilter;
use Boatrace\Scraper\Parsers\PreviewParser;
use Boatrace\Scraper\Parsers\ProgramParser;
use Boatrace\Scraper\Parsers\RacerParser;
use Boatrace\Scraper\Parsers\ResultParser;
use Boatrace\Scraper\Scraper;
use Boatrace\Scraper\ScraperDispatcher;
use Boatrace\Scraper\ScraperResponse;
use Boatrace\Scraper\Scrapers\OddsScraper;
use Boatrace\Scraper\Scrapers\PreviewScraper;
use Boatrace\Scraper\Scrapers\ProgramScraper;
use Boatrace\Scraper\Scrapers\ResultScraper;
use Boatrace\Scraper\Scrapers\StadiumScraper;
use Boatrace\Types\Contracts\BatchScraper\BatchScraper as BatchScraperContract;
use Boatrace\Types\Contracts\BatchScraper\BatchScraperDispatcher as BatchScraperDispatcherContract;
use Boatrace\Types\Contracts\BatchScraper\BatchScraperResponse as BatchScraperResponseContract;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Filter\FilterDispatcher as FilterDispatcherContract;
use Boatrace\Types\Contracts\Filter\OddsFilter as OddsFilterContract;
use Boatrace\Types\Contracts\Parser\PreviewParser as PreviewParserContract;
use Boatrace\Types\Contracts\Parser\ProgramParser as ProgramParserContract;
use Boatrace\Types\Contracts\Parser\RacerParser as RacerParserContract;
use Boatrace\Types\Contracts\Parser\ResultParser as ResultParserContract;
use Boatrace\Types\Contracts\Progress\ProgressDispatcher as ProgressDispatcherContract;
use Boatrace\Types\Contracts\Scraper\OddsScraper as OddsScraperContract;
use Boatrace\Types\Contracts\Scraper\PreviewScraper as PreviewScraperContract;
use Boatrace\Types\Contracts\Scraper\ProgramScraper as ProgramScraperContract;
use Boatrace\Types\Contracts\Scraper\ResultScraper as ResultScraperContract;
use Boatrace\Types\Contracts\Scraper\Scraper as ScraperContract;
use Boatrace\Types\Contracts\Scraper\ScraperDispatcher as ScraperDispatcherContract;
use Boatrace\Types\Contracts\Scraper\ScraperResponse as ScraperResponseContract;
use Boatrace\Types\Contracts\Scraper\StadiumScraper as StadiumScraperContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher as TrimmerDispatcherContract;
use Boatrace\Types\Contracts\Validator\ValidatorDispatcher as ValidatorDispatcherContract;

return [
    BatchScraperContract::class => \DI\autowire(BatchScraper::class)
        ->constructor(\DI\get(BatchScraperDispatcherContract::class)),
    BatchScraperDispatcherContract::class => \DI\autowire(BatchScraperDispatcher::class)
        ->constructor(
            \DI\get(ScraperDispatcherContract::class),
            \DI\get(ProgressDispatcherContract::class),
        ),
    BatchScraperResponseContract::class => function (
        \DI\Container $container,
        bool|array|null $value = null
    ) {
        return new BatchScraperResponse($value);
    },
    ScraperContract::class => \DI\autowire(Scraper::class)
        ->constructor(\DI\get(ScraperDispatcherContract::class)),
    ScraperDispatcherContract::class => \DI\autowire(ScraperDispatcher::class)
        ->constructor(
            \DI\get(StadiumScraperContract::class),
            \DI\get(ProgramScraperContract::class),
            \DI\get(PreviewScraperContract::class),
            \DI\get(OddsScraperContract::class),
            \DI\get(ResultScraperContract::class),
            \DI\get(ThrottlerDispatcherContract::class),
            \DI\get(ValidatorDispatcherContract::class),
        ),
    ScraperResponseContract::class => function (
        \DI\Container $container,
        float|bool|array|null $value = null
    ) {
        return new ScraperResponse($value);
    },
    StadiumScraperContract::class => \DI\autowire(StadiumScraper::class)
        ->constructor(
            \DI\get(BrowserDispatcherContract::class),
            \DI\get(ConverterDispatcherContract::class),
        ),
    ProgramScraperContract::class => \DI\autowire(ProgramScraper::class)
        ->constructor(
            \DI\get(BrowserDispatcherContract::class),
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(FilterDispatcherContract::class),
            \DI\get(RacerParserContract::class),
            \DI\get(ProgramParserContract::class),
        ),
    PreviewScraperContract::class => \DI\autowire(PreviewScraper::class)
        ->constructor(
            \DI\get(BrowserDispatcherContract::class),
            \DI\get(FilterDispatcherContract::class),
            \DI\get(RacerParserContract::class),
            \DI\get(PreviewParserContract::class),
        ),
    OddsScraperContract::class => \DI\autowire(OddsScraper::class)
        ->constructor(
            \DI\get(BrowserDispatcherContract::class),
            \DI\get(FilterDispatcherContract::class),
            \DI\get(OddsFilterContract::class),
            \DI\get(ThrottlerDispatcherContract::class),
        ),
    ResultScraperContract::class => \DI\autowire(ResultScraper::class)
        ->constructor(
            \DI\get(BrowserDispatcherContract::class),
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(FilterDispatcherContract::class),
            \DI\get(RacerParserContract::class),
            \DI\get(ResultParserContract::class),
        ),
    OddsFilterContract::class => \DI\autowire(OddsFilter::class)
        ->constructor(
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(TrimmerDispatcherContract::class),
        ),
    RacerParserContract::class => \DI\autowire(RacerParser::class)
        ->constructor(\DI\get(ConverterDispatcherContract::class)),
    ProgramParserContract::class => \DI\autowire(ProgramParser::class)
        ->constructor(
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(TrimmerDispatcherContract::class),
        ),
    PreviewParserContract::class => \DI\autowire(PreviewParser::class)
        ->constructor(
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(TrimmerDispatcherContract::class),
        ),
    ResultParserContract::class => \DI\autowire(ResultParser::class)
        ->constructor(
            \DI\get(ConverterDispatcherContract::class),
            \DI\get(TrimmerDispatcherContract::class),
        ),
];
