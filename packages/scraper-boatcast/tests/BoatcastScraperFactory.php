<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests;

use Boatrace\BoatcastScraper\BatchBoatcastScraperDispatcher;
use Boatrace\BoatcastScraper\BoatcastScraperDispatcher;
use Boatrace\BoatcastScraper\Scrapers\OddsScraper;
use Boatrace\BoatcastScraper\Scrapers\StadiumScraper;
use Boatrace\BoatcastScraper\Scrapers\TimeScraper;
use Boatrace\BoatcastScraper\Scrapers\VoteScraper;
use Boatrace\Support\Browser\BrowserDispatcher;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Json\JsonDispatcher;
use Boatrace\Support\Normalizer\NormalizerDispatcher;
use Boatrace\Support\Progress\ProgressDispatcher;
use Boatrace\Support\Throttler\ThrottlerDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use Boatrace\Support\Tsv\TsvDispatcher;
use Boatrace\Support\Validator\ValidatorDispatcher;

/**
 * @author shimomo
 */
final class BoatcastScraperFactory
{
    /**
     * @return \Boatrace\BoatcastScraper\Scrapers\StadiumScraper
     */
    public static function createStadiumScraper(): StadiumScraper
    {
        return new StadiumScraper(
            new BrowserDispatcher(),
            self::createConverter(),
            new JsonDispatcher(),
        );
    }

    /**
     * @return \Boatrace\BoatcastScraper\Scrapers\TimeScraper
     */
    public static function createTimeScraper(): TimeScraper
    {
        $converter = self::createConverter();

        return new TimeScraper(
            new BrowserDispatcher(),
            $converter,
            new NormalizerDispatcher($converter),
            new TsvDispatcher(),
        );
    }

    /**
     * @return \Boatrace\BoatcastScraper\Scrapers\VoteScraper
     */
    public static function createVoteScraper(): VoteScraper
    {
        return new VoteScraper(
            new BrowserDispatcher(),
            self::createConverter(),
            new ThrottlerDispatcher(self::createConverter(), 0.0),
            new TsvDispatcher(),
        );
    }

    /**
     * @return \Boatrace\BoatcastScraper\Scrapers\OddsScraper
     */
    public static function createOddsScraper(): OddsScraper
    {
        return new OddsScraper(
            new BrowserDispatcher(),
            self::createConverter(),
            new ThrottlerDispatcher(self::createConverter(), 0.0),
            new TsvDispatcher(),
        );
    }

    /**
     * @return \Boatrace\BoatcastScraper\BoatcastScraperDispatcher
     */
    public static function createBoatcastDispatcher(): BoatcastScraperDispatcher
    {
        return new BoatcastScraperDispatcher(
            self::createStadiumScraper(),
            self::createTimeScraper(),
            self::createVoteScraper(),
            self::createOddsScraper(),
            new ThrottlerDispatcher(self::createConverter(), 0.0),
            new ValidatorDispatcher(),
        );
    }

    /**
     * @return \Boatrace\BoatcastScraper\BatchBoatcastScraperDispatcher
     */
    public static function createBatchBoatcastDispatcher(): BatchBoatcastScraperDispatcher
    {
        return new BatchBoatcastScraperDispatcher(
            self::createBoatcastDispatcher(),
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
}
