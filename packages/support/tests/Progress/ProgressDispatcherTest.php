<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Progress;

use BadMethodCallException;
use Boatrace\Support\Progress\ProgressDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @author shimomo
 */
final class ProgressDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Symfony\Component\Console\Output\BufferedOutput
     */
    protected BufferedOutput $output;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Progress\ProgressDispatcher
     */
    protected ProgressDispatcher $progress;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->output = new BufferedOutput();
        $this->progress = new ProgressDispatcher($this->output);
    }

    /**
     * @return void
     */
    #[Test]
    public function isEnabledReturnsFalseByDefault(): void
    {
        $this->assertFalse($this->progress->isEnabled());
    }

    /**
     * @return void
     */
    #[Test]
    public function setEnabledUpdatesEnabled(): void
    {
        $this->progress->setEnabled(true);

        $this->assertTrue($this->progress->isEnabled());
    }

    /**
     * @return void
     */
    #[Test]
    public function startWritesMessage(): void
    {
        $this->progress->start(2, '出走表のスクレイピングを開始します');

        $this->assertStringContainsString('📊 出走表のスクレイピングを開始します', $this->output->fetch());
    }

    /**
     * @return void
     */
    #[Test]
    public function advanceWritesProgress(): void
    {
        $this->progress->start(2);
        $this->output->fetch();

        $this->progress->advance();
        $this->progress->advance();
        $this->progress->finish();

        $this->assertStringContainsString('2/2', $this->output->fetch());
    }

    /**
     * @return void
     */
    #[Test]
    public function finishWritesMessage(): void
    {
        $this->progress->start(2);
        $this->progress->advance(2);
        $this->output->fetch();

        $this->progress->finish('出走表のスクレイピングが完了しました（2件）');

        $this->assertStringContainsString(
            '✅ 出走表のスクレイピングが完了しました（2件）',
            $this->output->fetch()
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function advanceAndFinishDoNothingWithoutStart(): void
    {
        $this->progress->advance();
        $this->progress->finish();

        $this->assertSame('', trim($this->output->fetch()));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Progress\ProgressDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->progress->ghost();
    }
}
