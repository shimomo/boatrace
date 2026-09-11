<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests\Filters;

use BadMethodCallException;
use Boatrace\Scraper\Filters\OddsFilter;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class OddsFilterTest extends TestCase
{
    /**
     * @var non-empty-string
     */
    private const string HTML = <<<'HTML'
        <html lang="ja">
            <body>
                <span class="odds"> 12.3 </span>
                <span class="absent">欠場</span>
                <span class="range">3.4-5.6</span>
                <span class="brokenRange">欠場-5.6</span>
            </body>
        </html>
        HTML;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Scraper\Filters\OddsFilter
     */
    protected OddsFilter $oddsFilter;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected Crawler $crawler;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->oddsFilter = new OddsFilter(
            new ConverterDispatcher(new TrimmerDispatcher()),
            new TrimmerDispatcher()
        );

        $this->crawler = new Crawler(self::HTML);
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathReturnsOdds(): void
    {
        $this->assertSame(12.3, $this->oddsFilter->byXPath($this->crawler, '//span[@class="odds"]'));
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathReturnsNullWhenValueIsNotNumeric(): void
    {
        $this->assertNull($this->oddsFilter->byXPath($this->crawler, '//span[@class="absent"]'));
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathReturnsNullWhenElementIsMissing(): void
    {
        $this->assertNull($this->oddsFilter->byXPath($this->crawler, '//span[@class="ghost"]'));
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathAsRangeReturnsRange(): void
    {
        $this->assertSame(
            ['lower_limit' => 3.4, 'upper_limit' => 5.6],
            $this->oddsFilter->byXPathAsRange($this->crawler, '//span[@class="range"]')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathAsRangeReturnsNullsWhenValueIsNotNumeric(): void
    {
        $this->assertSame(
            ['lower_limit' => null, 'upper_limit' => null],
            $this->oddsFilter->byXPathAsRange($this->crawler, '//span[@class="brokenRange"]')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Scraper\Filters\OddsFilter::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->oddsFilter->ghost();
    }
}
