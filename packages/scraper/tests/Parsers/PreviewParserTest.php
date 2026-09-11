<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests\Parsers;

use BadMethodCallException;
use Boatrace\Scraper\Parsers\PreviewParser;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class PreviewParserTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Scraper\Parsers\PreviewParser
     */
    protected PreviewParser $previewParser;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $trimmer = new TrimmerDispatcher();

        $this->previewParser = new PreviewParser(new ConverterDispatcher($trimmer), $trimmer);
    }

    /**
     * @return void
     */
    #[Test]
    public function parseStartTimingReturnsSecondsAsFloat(): void
    {
        $this->assertSame(
            ['start_timing_source' => '.15', 'start_timing' => 0.15],
            $this->previewParser->parseStartTiming('.15')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseStartTimingReturnsFlyingAsNegative(): void
    {
        $this->assertSame(
            ['start_timing_source' => 'F.02', 'start_timing' => -0.02],
            $this->previewParser->parseStartTiming('F.02')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseStartTimingReturnsNullForLate(): void
    {
        $this->assertSame(
            ['start_timing_source' => 'L', 'start_timing' => null],
            $this->previewParser->parseStartTiming('L')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseStartTimingReturnsNullWhenValueIsEmpty(): void
    {
        $this->assertSame(
            ['start_timing_source' => null, 'start_timing' => null],
            $this->previewParser->parseStartTiming(null)
        );
        $this->assertSame(
            ['start_timing_source' => null, 'start_timing' => null],
            $this->previewParser->parseStartTiming('')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseWeatherReturnsNumber(): void
    {
        $this->assertSame(
            ['weather_number_source' => '曇り', 'weather_number' => 2],
            $this->previewParser->parseWeather('曇り')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseWindDirectionReturnsNumber(): void
    {
        $this->assertSame(
            ['wind_direction_number_source' => '北', 'wind_direction_number' => 1],
            $this->previewParser->parseWindDirection('1')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parsePartsReadsPrintedQuantity(): void
    {
        $this->assertSame(
            ['parts' => [
                ['number_source' => 'リング', 'number' => 2, 'quantity' => 1],
                ['number_source' => 'ピストン', 'number' => 1, 'quantity' => 2],
            ]],
            $this->previewParser->parseParts(['リング×1', 'ピストン×2'])
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parsePartsKeepsQuantityNullWhenNotPrinted(): void
    {
        $this->assertSame(
            ['parts' => [
                ['number_source' => 'キャブ', 'number' => 4, 'quantity' => null],
            ]],
            $this->previewParser->parseParts(['キャブ'])
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parsePartsDistinguishesNoExchangeFromMissingCell(): void
    {
        $this->assertSame(['parts' => []], $this->previewParser->parseParts([]));
        $this->assertSame(['parts' => null], $this->previewParser->parseParts(null));
    }

    /**
     * @return void
     */
    #[Test]
    public function parsePropellerKeepsThePrintedText(): void
    {
        $this->assertSame(['propeller' => '新'], $this->previewParser->parsePropeller('新'));
        $this->assertSame(['propeller' => null], $this->previewParser->parsePropeller(''));
    }

    /**
     * @return void
     */
    #[Test]
    public function parseExhibitionTimeReturnsFloat(): void
    {
        $this->assertSame(
            ['exhibition_time_source' => '6.78', 'exhibition_time' => 6.78],
            $this->previewParser->parseExhibitionTime('6.78')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseWeatherAsOfReadsTheRaceTheReadingWasTakenAt(): void
    {
        $this->assertSame(
            ['weather_as_of_source' => '11R時点', 'weather_as_of_race_number' => 11, 'weather_as_of_time' => null],
            $this->previewParser->parseWeatherAsOf('水面気象情報　11R時点')
        );
        $this->assertSame(
            ['weather_as_of_source' => '3R時点', 'weather_as_of_race_number' => 3, 'weather_as_of_time' => null],
            $this->previewParser->parseWeatherAsOf('水面気象情報3R時点')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseWeatherAsOfReadsTheClockTimeOfALiveReading(): void
    {
        $this->assertSame(
            ['weather_as_of_source' => '18:04現在', 'weather_as_of_race_number' => null, 'weather_as_of_time' => '18:04'],
            $this->previewParser->parseWeatherAsOf('水面気象情報 18:04現在')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseWeatherAsOfKeepsAnUnknownWordingWithoutValues(): void
    {
        $this->assertSame(
            ['weather_as_of_source' => '計測中', 'weather_as_of_race_number' => null, 'weather_as_of_time' => null],
            $this->previewParser->parseWeatherAsOf('水面気象情報　計測中')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseWeatherAsOfReturnsNullWhenHeadingIsEmpty(): void
    {
        $empty = ['weather_as_of_source' => null, 'weather_as_of_race_number' => null, 'weather_as_of_time' => null];

        $this->assertSame($empty, $this->previewParser->parseWeatherAsOf(null));
        $this->assertSame($empty, $this->previewParser->parseWeatherAsOf(''));
        $this->assertSame($empty, $this->previewParser->parseWeatherAsOf("水面気象情報　\u{00A0}"));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Scraper\Parsers\PreviewParser::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->previewParser->ghost();
    }
}
