<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Progress;

use Boatrace\Support\Progress\Progress;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ProgressTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function setEnabledUpdatesSharedInstance(): void
    {
        Progress::setEnabled(true);

        $this->assertTrue(Progress::isEnabled()->getValue());

        Progress::setEnabled(false);

        $this->assertFalse(Progress::isEnabled()->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function startAndAdvanceAndFinishWriteNothingWhenDisabled(): void
    {
        ob_start();

        Progress::start(1, '開始');
        Progress::advance();
        Progress::finish('完了');

        $this->assertSame('', (string) ob_get_clean());
    }
}
