<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests;

use Boatrace\Scraper\BatchScraperDispatcher;
use Boatrace\Scraper\Filters\OddsFilter;
use Boatrace\Scraper\Parsers\PreviewParser;
use Boatrace\Scraper\Parsers\ProgramParser;
use Boatrace\Scraper\Parsers\RacerParser;
use Boatrace\Scraper\Parsers\ResultParser;
use Boatrace\Scraper\ScraperDispatcher;
use Boatrace\Scraper\Scrapers\OddsScraper;
use Boatrace\Scraper\Scrapers\PreviewScraper;
use Boatrace\Scraper\Scrapers\ProgramScraper;
use Boatrace\Scraper\Scrapers\ResultScraper;
use Boatrace\Scraper\Scrapers\StadiumScraper;
use Boatrace\Support\Browser\BrowserDispatcher;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Filter\FilterDispatcher;
use Boatrace\Support\Progress\ProgressDispatcher;
use Boatrace\Support\Throttler\ThrottlerDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use Boatrace\Support\Validator\ValidatorDispatcher;

/**
 * @author shimomo
 */
final class ScraperFactory
{
    /**
     * @return \Boatrace\Scraper\Scrapers\StadiumScraper
     */
    public static function createStadiumScraper(): StadiumScraper
    {
        return new StadiumScraper(new BrowserDispatcher(), self::createConverter());
    }

    /**
     * @return \Boatrace\Scraper\Scrapers\ProgramScraper
     */
    public static function createProgramScraper(): ProgramScraper
    {
        $converter = self::createConverter();

        return new ProgramScraper(
            new BrowserDispatcher(),
            $converter,
            self::createFilter(),
            new RacerParser($converter),
            new ProgramParser($converter, new TrimmerDispatcher()),
        );
    }

    /**
     * @return \Boatrace\Scraper\Scrapers\PreviewScraper
     */
    public static function createPreviewScraper(): PreviewScraper
    {
        $converter = self::createConverter();

        return new PreviewScraper(
            new BrowserDispatcher(),
            self::createFilter(),
            new RacerParser($converter),
            new PreviewParser($converter, new TrimmerDispatcher()),
        );
    }

    /**
     * @return \Boatrace\Scraper\Scrapers\OddsScraper
     */
    public static function createOddsScraper(): OddsScraper
    {
        $converter = self::createConverter();

        return new OddsScraper(
            new BrowserDispatcher(),
            self::createFilter(),
            new OddsFilter($converter, new TrimmerDispatcher()),
            self::createThrottler(),
        );
    }

    /**
     * @return \Boatrace\Scraper\Scrapers\ResultScraper
     */
    public static function createResultScraper(): ResultScraper
    {
        $converter = self::createConverter();

        return new ResultScraper(
            new BrowserDispatcher(),
            $converter,
            self::createFilter(),
            new RacerParser($converter),
            new ResultParser($converter, new TrimmerDispatcher()),
        );
    }

    /**
     * @return \Boatrace\Scraper\ScraperDispatcher
     */
    public static function createScraperDispatcher(): ScraperDispatcher
    {
        return new ScraperDispatcher(
            self::createStadiumScraper(),
            self::createProgramScraper(),
            self::createPreviewScraper(),
            self::createOddsScraper(),
            self::createResultScraper(),
            self::createThrottler(),
            new ValidatorDispatcher(),
        );
    }

    /**
     * @return \Boatrace\Scraper\BatchScraperDispatcher
     */
    public static function createBatchScraperDispatcher(): BatchScraperDispatcher
    {
        return new BatchScraperDispatcher(
            self::createScraperDispatcher(),
            new ProgressDispatcher(),
        );
    }

    /**
     * @return \Boatrace\Support\Converter\ConverterDispatcher
     */
    private static function createConverter(): ConverterDispatcher
    {
        return new ConverterDispatcher(new TrimmerDispatcher());
    }

    /**
     * @return \Boatrace\Support\Filter\FilterDispatcher
     */
    private static function createFilter(): FilterDispatcher
    {
        $trimmer = new TrimmerDispatcher();

        return new FilterDispatcher(new ConverterDispatcher($trimmer), $trimmer);
    }

    /**
     * @return \Boatrace\Support\Throttler\ThrottlerDispatcher
     */
    private static function createThrottler(): ThrottlerDispatcher
    {
        return new ThrottlerDispatcher(self::createConverter(), 0.0);
    }
}
