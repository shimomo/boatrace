<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Filter;

use Boatrace\Support\Filter\Filter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class FilterTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function byXPathReturnsNormalizedText(): void
    {
        $crawler = new Crawler('<html lang="ja"><body><span class="title"> 　ＧＩ　</span></body></html>');

        $this->assertSame('GI', Filter::byXPath($crawler, '//span[@class="title"]')->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function byXPathReturnsNullWhenElementIsMissing(): void
    {
        $crawler = new Crawler('<html lang="ja"><body></body></html>');

        $this->assertNull(Filter::byXPath($crawler, '//span[@class="title"]')->getValue());
    }
}
