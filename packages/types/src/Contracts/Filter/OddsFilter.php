<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Filter;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
interface OddsFilter extends Filter
{
    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @return ?float
     */
    public function byXPath(Crawler $crawler, string $xpath): ?float;

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @return array{
     *     lower_limit: ?float,
     *     upper_limit: ?float,
     * }
     */
    public function byXPathAsRange(Crawler $crawler, string $xpath): array;
}
