<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests\Parsers;

use BadMethodCallException;
use Boatrace\Scraper\Parsers\ResultParser;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ResultParserTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Scraper\Parsers\ResultParser
     */
    protected ResultParser $resultParser;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $trimmer = new TrimmerDispatcher();

        $this->resultParser = new ResultParser(new ConverterDispatcher($trimmer), $trimmer);
    }

    /**
     * @return void
     */
    #[Test]
    public function parsePlaceReturnsNumber(): void
    {
        $this->assertSame(
            ['place_number_source' => '1', 'place_number' => 1],
            $this->resultParser->parsePlace('1')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parsePlaceReturnsNumberForDisqualifications(): void
    {
        $this->assertSame(
            ['place_number_source' => 'F', 'place_number' => 14],
            $this->resultParser->parsePlace('F')
        );
        $this->assertSame(
            ['place_number_source' => '転', 'place_number' => 9],
            $this->resultParser->parsePlace('転')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parsePlaceReturnsNullWhenValueIsEmpty(): void
    {
        $this->assertSame(
            ['place_number_source' => null, 'place_number' => null],
            $this->resultParser->parsePlace(null)
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseStartTimingReturnsFlyingAsNegative(): void
    {
        $this->assertSame(
            ['start_timing_source' => 'F.01', 'start_timing' => -0.01],
            $this->resultParser->parseStartTiming('F.01')
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
            $this->resultParser->parseStartTiming('L')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseWeatherReturnsNumber(): void
    {
        $this->assertSame(
            ['weather_number_source' => '晴', 'weather_number' => 1],
            $this->resultParser->parseWeather('晴')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseRemarksKeepsThePrintedText(): void
    {
        $this->assertSame(
            ['remarks' => '【返還艇あり】'],
            $this->resultParser->parseRemarks('【返還艇あり】')
        );
        $this->assertSame(['remarks' => null], $this->resultParser->parseRemarks(''));
    }

    /**
     * @return void
     */
    #[Test]
    public function parseWaveHeightReturnsNumber(): void
    {
        $this->assertSame(
            ['wave_height_source' => '2cm', 'wave_height' => 2],
            $this->resultParser->parseWaveHeight('2cm')
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
            'Call to undefined method `Boatrace\Scraper\Parsers\ResultParser::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->resultParser->ghost();
    }
}
