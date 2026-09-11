<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Filter;

use BadMethodCallException;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Filter\FilterDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class FilterDispatcherTest extends TestCase
{
    /**
     * @var non-empty-string
     */
    private const string HTML = <<<'HTML'
        <html lang="ja">
            <body>
                <div class="is-grade is-G1A">
                    <span class="title"> 　ＧＩ　福岡チャンピオンカップ　</span>
                    <span class="wind is-wind14">北西</span>
                </div>
                <ul class="labelGroup1">
                    <li><span>ピストン×２</span></li>
                    <li><span>キャブ&nbsp;</span></li>
                </ul>
            </body>
        </html>
        HTML;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Filter\FilterDispatcher
     */
    protected FilterDispatcher $filter;

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
        $this->filter = new FilterDispatcher(
            new ConverterDispatcher(new TrimmerDispatcher()),
            new TrimmerDispatcher()
        );

        $this->crawler = new Crawler(self::HTML);
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathReturnsNormalizedText(): void
    {
        $this->assertSame(
            'GI 福岡チャンピオンカップ',
            $this->filter->byXPath($this->crawler, '//span[@class="title"]')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathReturnsNullWhenElementIsMissing(): void
    {
        $this->assertNull($this->filter->byXPath($this->crawler, '//span[@class="ghost"]'));
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathAsListReturnsEachText(): void
    {
        $this->assertSame(
            ['ピストン×2', 'キャブ'],
            $this->filter->byXPathAsList($this->crawler, '//ul[@class="labelGroup1"]/li')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathAsListReturnsEmptyArrayWhenElementIsMissing(): void
    {
        $this->assertSame([], $this->filter->byXPathAsList($this->crawler, '//ul[@class="ghost"]/li'));
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathAsAttributeReturnsAttribute(): void
    {
        $this->assertSame(
            'is-grade is-G1A',
            $this->filter->byXPathAsAttribute($this->crawler, '//div', 'class')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathAsAttributeReturnsNullWhenElementIsMissing(): void
    {
        $this->assertNull($this->filter->byXPathAsAttribute($this->crawler, '//table', 'class'));
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathAsPatternReturnsMatchedValue(): void
    {
        $this->assertSame(
            'G1A',
            $this->filter->byXPathAsPattern($this->crawler, '//div', 'class', '/is-([a-zA-Z0-9]+)$/u')
        );

        $this->assertSame(
            '14',
            $this->filter->byXPathAsPattern($this->crawler, '//span[@class!="title"]', 'class', '/is-wind(\d+)/u')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathAsPatternReturnsNullWhenPatternDoesNotMatch(): void
    {
        $this->assertNull(
            $this->filter->byXPathAsPattern($this->crawler, '//div', 'class', '/is-wind(\d+)/u')
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
            'Call to undefined method `Boatrace\Support\Filter\FilterDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->filter->ghost();
    }
}
