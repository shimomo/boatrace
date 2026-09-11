<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Filter;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
interface FilterDispatcher extends Filter
{
    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @return ?string
     */
    public function byXPath(Crawler $crawler, string $xpath): ?string;

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @return list<string>
     */
    public function byXPathAsList(Crawler $crawler, string $xpath): array;

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @param non-empty-string $attribute
     * @return ?string
     */
    public function byXPathAsAttribute(Crawler $crawler, string $xpath, string $attribute): ?string;

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @param non-empty-string $attribute
     * @param non-empty-string $pattern
     * @return ?string
     */
    public function byXPathAsPattern(
        Crawler $crawler,
        string $xpath,
        string $attribute,
        string $pattern
    ): ?string;
}
