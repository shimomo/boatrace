<?php

declare(strict_types=1);

namespace Boatrace\Support\Filter;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Filter\Filter as FilterContract;
use Boatrace\Types\Contracts\Filter\FilterDispatcher as FilterDispatcherContract;
use Boatrace\Types\Contracts\Filter\FilterResponse as FilterResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Filter\FilterResponse
 *     byXPath(\Symfony\Component\DomCrawler\Crawler $crawler, string $xpath)
 * @method static \Boatrace\Types\Contracts\Filter\FilterResponse
 *     byXPathAsAttribute(\Symfony\Component\DomCrawler\Crawler $crawler, string $xpath, string $attribute)
 * @method static \Boatrace\Types\Contracts\Filter\FilterResponse
 *     byXPathAsPattern(\Symfony\Component\DomCrawler\Crawler $crawler, string $xpath, string $attribute, string $pattern)
 * @author shimomo
 */
final class Filter implements FilterContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Filter\FilterDispatcher $filter
     */
    public function __construct(private readonly FilterDispatcherContract $filter)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Filter\FilterResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): FilterResponseContract
    {
        return self::toResponse(FilterResponseContract::class, $this->filter->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Filter\FilterResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): FilterResponseContract
    {
        return self::assertResponse(
            FilterResponseContract::class,
            CoreContainer::getInstance(FilterContract::class)->$name(...$arguments),
        );
    }
}
