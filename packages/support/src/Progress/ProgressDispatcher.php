<?php

declare(strict_types=1);

namespace Boatrace\Support\Progress;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Progress\ProgressDispatcher as ProgressDispatcherContract;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author shimomo
 */
final class ProgressDispatcher implements ProgressDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @var non-empty-string
     */
    private const string FORMAT = ' %current%/%max% [%bar%] %percent:3s%% ⏱️ %elapsed:6s% / %estimated:-6s%';

    /**
     * @var ?\Symfony\Component\Console\Helper\ProgressBar
     */
    private ?ProgressBar $progressBar = null;

    /**
     * @param ?\Symfony\Component\Console\Output\OutputInterface $output
     * @param bool $enabled
     */
    public function __construct(
        private readonly ?OutputInterface $output = null,
        private bool $enabled = false,
    ) {
        //
    }

    /**
     * @return bool
     */
    #[\Override]
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return void
     */
    #[\Override]
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @param int<0, max> $totalSteps
     * @param ?string $message
     * @return void
     */
    #[\Override]
    public function start(int $totalSteps, ?string $message = null): void
    {
        $output = $this->getOutput();

        if ($message !== null) {
            $output->writeln(sprintf('<info>📊 %s</info>', $message));
        }

        if ($totalSteps < 1) {
            $this->progressBar = null;

            return;
        }

        $this->progressBar = new ProgressBar($output, $totalSteps);
        $this->progressBar->setFormat(self::FORMAT);
        $this->progressBar->start();
    }

    /**
     * @param int<1, max> $step
     * @return void
     */
    #[\Override]
    public function advance(int $step = 1): void
    {
        $this->progressBar?->advance($step);
    }

    /**
     * @param ?string $message
     * @return void
     */
    #[\Override]
    public function finish(?string $message = null): void
    {
        $this->progressBar?->finish();
        $this->progressBar = null;

        $output = $this->getOutput();
        $output->writeln('');

        if ($message !== null) {
            $output->writeln(sprintf('<info>✅ %s</info>', $message));
            $output->writeln('');
        }
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    private function getOutput(): OutputInterface
    {
        return $this->output ?? ($this->enabled ? new ConsoleOutput() : new NullOutput());
    }
}
