<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Filters;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Filter\OddsFilter as OddsFilterContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher as TrimmerDispatcherContract;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class OddsFilter implements OddsFilterContract
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
     * @return ?float
     */
    #[\Override]
    public function byXPath(Crawler $crawler, string $xpath): ?float
    {
        $element = $crawler->filterXPath($xpath);

        if (!$element->count()) {
            return null;
        }

        $value = $this->trimmer->trimOrNull($element->text());

        return is_numeric($value) ? $this->converter->toFloat($value) : null;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param non-empty-string $xpath
     * @return array{
     *     lower_limit: ?float,
     *     upper_limit: ?float,
     * }
     */
    #[\Override]
    public function byXPathAsRange(Crawler $crawler, string $xpath): array
    {
        $response = ['lower_limit' => null, 'upper_limit' => null];

        $element = $crawler->filterXPath($xpath);

        if ($element->count() && count($odds = explode('-', $element->text())) === 2) {
            $lowerLimit = $this->trimmer->trimOrNull(array_shift($odds));
            $upperLimit = $this->trimmer->trimOrNull(array_shift($odds));

            if (is_numeric($lowerLimit) && is_numeric($upperLimit)) {
                $response['lower_limit'] = $this->converter->toFloat($lowerLimit);
                $response['upper_limit'] = $this->converter->toFloat($upperLimit);
            }
        }

        return $response;
    }
}
