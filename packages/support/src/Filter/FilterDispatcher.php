<?php

declare(strict_types=1);

namespace Boatrace\Support\Filter;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Filter\FilterDispatcher as FilterDispatcherContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher as TrimmerDispatcherContract;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class FilterDispatcher implements FilterDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     * @param \Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher $trimmer
     */
    public function __construct(
        private readonly ConverterDispatcherContract $converter,
        private readonly TrimmerDispatcherContract $trimmer,
    ) {
        //
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @return ?string
     */
    #[\Override]
    public function byXPath(Crawler $crawler, string $xpath): ?string
    {
        $element = $crawler->filterXPath($xpath);

        if (!$element->count()) {
            return null;
        }

        return $this->trimmer->trimOrNull($this->converter->toKana($element->text()));
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @return list<string>
     */
    #[\Override]
    public function byXPathAsList(Crawler $crawler, string $xpath): array
    {
        return $crawler->filterXPath($xpath)->each(
            fn(Crawler $node): string => $this->trimmer->trimOrNull($this->converter->toKana($node->text())) ?? ''
        );
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @param non-empty-string $attribute
     * @return ?string
     */
    #[\Override]
    public function byXPathAsAttribute(Crawler $crawler, string $xpath, string $attribute): ?string
    {
        $element = $crawler->filterXPath($xpath);

        if (!$element->count()) {
            return null;
        }

        return $element->attr($attribute);
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @param non-empty-string $attribute
     * @param non-empty-string $pattern
     * @return ?string
     */
    #[\Override]
    public function byXPathAsPattern(
        Crawler $crawler,
        string $xpath,
        string $attribute,
        string $pattern
    ): ?string {
        $value = $this->byXPathAsAttribute($crawler, $xpath, $attribute);

        if ($value === null || !preg_match($pattern, $value, $matches)) {
            return null;
        }

        return $matches[1] ?? null;
    }
}
