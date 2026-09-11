<?php

declare(strict_types=1);

namespace Boatrace\Support\Timing;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Timing\TimingDispatcher as TimingDispatcherContract;

/**
 * @author shimomo
 */
final class TimingDispatcher implements TimingDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @param list<string>|string|null $value
     * @return array<non-empty-string, array{dur: ?float, desc: ?string}>
     */
    #[\Override]
    public function parse(array|string|null $value): array
    {
        if ($value === null) {
            return [];
        }

        $timings = [];

        foreach (is_array($value) ? $value : [$value] as $line) {
            foreach ($this->split($line, ',') as $metric) {
                $parsed = $this->parseMetric($metric);

                if ($parsed !== null) {
                    $timings[$parsed['name']] = ['dur' => $parsed['dur'], 'desc' => $parsed['desc']];
                }
            }
        }

        return $timings;
    }

    /**
     * @param string $metric
     * @return ?array{name: non-empty-string, dur: ?float, desc: ?string}
     */
    private function parseMetric(string $metric): ?array
    {
        $parts = $this->split($metric, ';');
        $name = trim(array_shift($parts) ?? '');

        if ($name === '') {
            return null;
        }

        $parameters = [];

        foreach ($parts as $part) {
            $position = strpos($part, '=');

            if ($position === false) {
                continue;
            }

            $parameters[strtolower(trim(substr($part, 0, $position)))] = $this->unquote(
                substr($part, $position + 1)
            );
        }

        $duration = $parameters['dur'] ?? null;
        $description = $parameters['desc'] ?? null;

        return [
            'name' => $name,
            'dur' => $duration !== null && is_numeric($duration) ? (float) $duration : null,
            'desc' => $description !== '' ? $description : null,
        ];
    }

    /**
     * @param string $value
     * @param non-empty-string $delimiter
     * @return list<string>
     */
    private function split(string $value, string $delimiter): array
    {
        $parts = [];
        $buffer = '';
        $quoted = false;
        $escaped = false;

        foreach (str_split($value) as $character) {
            if ($escaped) {
                $buffer .= $character;
                $escaped = false;
                continue;
            }

            if ($quoted && $character === '\\') {
                $buffer .= $character;
                $escaped = true;
                continue;
            }

            if ($character === '"') {
                $quoted = !$quoted;
                $buffer .= $character;
                continue;
            }

            if (!$quoted && $character === $delimiter) {
                $parts[] = $buffer;
                $buffer = '';
                continue;
            }

            $buffer .= $character;
        }

        $parts[] = $buffer;

        return $parts;
    }

    /**
     * @param string $value
     * @return string
     */
    private function unquote(string $value): string
    {
        $value = trim($value);

        if (strlen($value) >= 2 && str_starts_with($value, '"') && str_ends_with($value, '"')) {
            return preg_replace('/\\\\(.)/', '$1', substr($value, 1, -1)) ?? '';
        }

        return $value;
    }
}
