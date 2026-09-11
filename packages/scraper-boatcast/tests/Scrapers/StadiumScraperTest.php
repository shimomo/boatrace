<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Tests\Scrapers;

use BadMethodCallException;
use Boatrace\BoatcastScraper\Tests\BoatcastScraperFactory;
use Boatrace\BoatcastScraper\Tests\MockBrowser;
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
        $response = BoatcastScraperFactory::createStadiumScraper()
            ->scrape('2026-08-01', MockBrowser::create('holding.json'));

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
    public function scrapeReturnsEmptyArrayWhenRequestFails(): void
    {
        $response = BoatcastScraperFactory::createStadiumScraper()
            ->scrape('2026-08-01', MockBrowser::create(null, 403));

        $this->assertSame([], $response);
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\BoatcastScraper\Scrapers\StadiumScraper::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        BoatcastScraperFactory::createStadiumScraper()->ghost();
    }
}
