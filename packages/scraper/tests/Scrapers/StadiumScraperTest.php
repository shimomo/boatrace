<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests\Scrapers;

use Boatrace\Scraper\Tests\MockBrowser;
use Boatrace\Scraper\Tests\ScraperFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class StadiumScraperTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function scrapeReturnsActiveStadiums(): void
    {
        $response = ScraperFactory::createStadiumScraper()
            ->scrape('2026-08-01', MockBrowser::create('stadium.html'));

        $this->assertSame([
            1 => '桐生',
            5 => '多摩川',
            6 => '浜名湖',
            8 => '常滑',
            9 => '津',
            10 => '三国',
            11 => 'びわこ',
            13 => '尼崎',
            15 => '丸亀',
            16 => '児島',
            18 => '徳山',
            20 => '若松',
            22 => '福岡',
            23 => '唐津',
        ], $response);
    }

    /**
     * @return void
     */
    #[Test]
    public function scrapeSkipsUnknownStadium(): void
    {
        $response = ScraperFactory::createStadiumScraper()
            ->scrape('2026-08-01', MockBrowser::create('stadium-unknown.html'));

        $this->assertArrayNotHasKey(1, $response);
        $this->assertCount(13, $response);
        $this->assertSame('多摩川', $response[5]);
        $this->assertSame('唐津', $response[23]);
    }
}
