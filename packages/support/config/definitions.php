<?php

declare(strict_types=1);

use Boatrace\Support\Browser\Browser;
use Boatrace\Support\Browser\BrowserDispatcher;
use Boatrace\Support\Browser\BrowserResponse;
use Boatrace\Support\Converter\Converter;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Converter\ConverterResponse;
use Boatrace\Support\Filter\Filter;
use Boatrace\Support\Filter\FilterDispatcher;
use Boatrace\Support\Filter\FilterResponse;
use Boatrace\Support\Json\Json;
use Boatrace\Support\Json\JsonDispatcher;
use Boatrace\Support\Json\JsonResponse;
use Boatrace\Support\Normalizer\Normalizer;
use Boatrace\Support\Normalizer\NormalizerDispatcher;
use Boatrace\Support\Normalizer\NormalizerResponse;
use Boatrace\Support\Progress\Progress;
use Boatrace\Support\Progress\ProgressDispatcher;
use Boatrace\Support\Progress\ProgressResponse;
use Boatrace\Support\Throttler\Throttler;
use Boatrace\Support\Throttler\ThrottlerDispatcher;
use Boatrace\Support\Throttler\ThrottlerResponse;
use Boatrace\Support\Timing\Timing;
use Boatrace\Support\Timing\TimingDispatcher;
use Boatrace\Support\Timing\TimingResponse;
use Boatrace\Support\Trimmer\Trimmer;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use Boatrace\Support\Trimmer\TrimmerResponse;
use Boatrace\Support\Tsv\Tsv;
use Boatrace\Support\Tsv\TsvDispatcher;
use Boatrace\Support\Tsv\TsvResponse;
use Boatrace\Support\Validator\Validator;
use Boatrace\Support\Validator\ValidatorDispatcher;
use Boatrace\Support\Validator\ValidatorResponse;
use Boatrace\Types\Contracts\Browser\Browser as BrowserContract;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Browser\BrowserObserver as BrowserObserverContract;
use Boatrace\Types\Contracts\Browser\BrowserResponse as BrowserResponseContract;
use Boatrace\Types\Contracts\Converter\Converter as ConverterContract;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Converter\ConverterResponse as ConverterResponseContract;
use Boatrace\Types\Contracts\Filter\Filter as FilterContract;
use Boatrace\Types\Contracts\Filter\FilterDispatcher as FilterDispatcherContract;
use Boatrace\Types\Contracts\Filter\FilterResponse as FilterResponseContract;
use Boatrace\Types\Contracts\Json\Json as JsonContract;
use Boatrace\Types\Contracts\Json\JsonDispatcher as JsonDispatcherContract;
use Boatrace\Types\Contracts\Json\JsonResponse as JsonResponseContract;
use Boatrace\Types\Contracts\Normalizer\Normalizer as NormalizerContract;
use Boatrace\Types\Contracts\Normalizer\NormalizerDispatcher as NormalizerDispatcherContract;
use Boatrace\Types\Contracts\Normalizer\NormalizerResponse as NormalizerResponseContract;
use Boatrace\Types\Contracts\Progress\Progress as ProgressContract;
use Boatrace\Types\Contracts\Progress\ProgressDispatcher as ProgressDispatcherContract;
use Boatrace\Types\Contracts\Progress\ProgressResponse as ProgressResponseContract;
use Boatrace\Types\Contracts\Throttler\Throttler as ThrottlerContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerResponse as ThrottlerResponseContract;
use Boatrace\Types\Contracts\Timing\Timing as TimingContract;
use Boatrace\Types\Contracts\Timing\TimingDispatcher as TimingDispatcherContract;
use Boatrace\Types\Contracts\Timing\TimingResponse as TimingResponseContract;
use Boatrace\Types\Contracts\Trimmer\Trimmer as TrimmerContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher as TrimmerDispatcherContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerResponse as TrimmerResponseContract;
use Boatrace\Types\Contracts\Tsv\Tsv as TsvContract;
use Boatrace\Types\Contracts\Tsv\TsvDispatcher as TsvDispatcherContract;
use Boatrace\Types\Contracts\Tsv\TsvResponse as TsvResponseContract;
use Boatrace\Types\Contracts\Validator\Validator as ValidatorContract;
use Boatrace\Types\Contracts\Validator\ValidatorDispatcher as ValidatorDispatcherContract;
use Boatrace\Types\Contracts\Validator\ValidatorResponse as ValidatorResponseContract;
use Symfony\Component\BrowserKit\HttpBrowser;

return [
    BrowserContract::class => \DI\autowire(Browser::class)
        ->constructor(\DI\get(BrowserDispatcherContract::class)),
    BrowserDispatcherContract::class => \DI\autowire(BrowserDispatcher::class)
        ->constructor(
            [],
            null,
            \DI\get(TimingDispatcherContract::class),
            \DI\get(BrowserObserverContract::class),
            3
        ),
    BrowserObserverContract::class => \DI\value(null),
    BrowserResponseContract::class => function (\DI\Container $container, ?HttpBrowser $value = null) {
        return new BrowserResponse($value);
    },
    ConverterContract::class => \DI\autowire(Converter::class)
        ->constructor(\DI\get(ConverterDispatcherContract::class)),
    ConverterDispatcherContract::class => \DI\autowire(ConverterDispatcher::class)
        ->constructor(\DI\get(TrimmerDispatcherContract::class)),
    ConverterResponseContract::class => function (
        \DI\Container $container,
        int|float|string|array|UnitEnum|null $value = null
    ) {
        return new ConverterResponse($value);
    },
    FilterContract::class => \DI\autowire(Filter::class)
        ->constructor(\DI\get(FilterDispatcherContract::class)),
    FilterDispatcherContract::class => \DI\autowire(FilterDispatcher::class)
        ->constructor(\DI\get(ConverterDispatcherContract::class), \DI\get(TrimmerDispatcherContract::class)),
    FilterResponseContract::class => function (\DI\Container $container, ?string $value = null) {
        return new FilterResponse($value);
    },
    JsonContract::class => \DI\autowire(Json::class)
        ->constructor(\DI\get(JsonDispatcherContract::class)),
    JsonDispatcherContract::class => \DI\autowire(JsonDispatcher::class),
    JsonResponseContract::class => function (\DI\Container $container, ?array $value = null) {
        return new JsonResponse($value);
    },
    NormalizerContract::class => \DI\autowire(Normalizer::class)
        ->constructor(\DI\get(NormalizerDispatcherContract::class)),
    NormalizerDispatcherContract::class => \DI\autowire(NormalizerDispatcher::class)
        ->constructor(\DI\get(ConverterDispatcherContract::class)),
    NormalizerResponseContract::class => function (
        \DI\Container $container,
        int|float|string|array|null $value = null
    ) {
        return new NormalizerResponse($value);
    },
    ProgressContract::class => \DI\autowire(Progress::class)
        ->constructor(\DI\get(ProgressDispatcherContract::class)),
    ProgressDispatcherContract::class => \DI\autowire(ProgressDispatcher::class)
        ->constructor(null, false),
    ProgressResponseContract::class => function (\DI\Container $container, ?bool $value = null) {
        return new ProgressResponse($value);
    },
    ThrottlerContract::class => \DI\autowire(Throttler::class)
        ->constructor(\DI\get(ThrottlerDispatcherContract::class)),
    ThrottlerDispatcherContract::class => \DI\autowire(ThrottlerDispatcher::class)
        ->constructor(\DI\get(ConverterDispatcherContract::class), 1.0),
    ThrottlerResponseContract::class => function (\DI\Container $container, ?float $value = null) {
        return new ThrottlerResponse($value);
    },
    TimingContract::class => \DI\autowire(Timing::class)
        ->constructor(\DI\get(TimingDispatcherContract::class)),
    TimingDispatcherContract::class => \DI\autowire(TimingDispatcher::class),
    TimingResponseContract::class => function (\DI\Container $container, ?array $value = null) {
        return new TimingResponse($value);
    },
    TrimmerContract::class => \DI\autowire(Trimmer::class)
        ->constructor(\DI\get(TrimmerDispatcherContract::class)),
    TrimmerDispatcherContract::class => \DI\autowire(TrimmerDispatcher::class),
    TrimmerResponseContract::class => function (\DI\Container $container, ?string $value = null) {
        return new TrimmerResponse($value);
    },
    TsvContract::class => \DI\autowire(Tsv::class)
        ->constructor(\DI\get(TsvDispatcherContract::class)),
    TsvDispatcherContract::class => \DI\autowire(TsvDispatcher::class),
    TsvResponseContract::class => function (\DI\Container $container, ?array $value = null) {
        return new TsvResponse($value);
    },
    ValidatorContract::class => \DI\autowire(Validator::class)
        ->constructor(\DI\get(ValidatorDispatcherContract::class)),
    ValidatorDispatcherContract::class => \DI\autowire(ValidatorDispatcher::class),
    ValidatorResponseContract::class => function (\DI\Container $container, ?int $value = null) {
        return new ValidatorResponse($value);
    },
];
