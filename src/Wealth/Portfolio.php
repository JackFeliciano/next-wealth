<?php

declare(strict_types=1);

namespace NextWealth\Wealth;

/**
 * A client's portfolio. It holds the lines, joins them into a value, and
 * splits that value by the kind of asset so a manager can see the shape of
 * the book on one page.
 */
final class Portfolio
{
    /** @var array<string, Holding> */
    private array $lines = [];

    public function __construct(
        public readonly string $client,
        public readonly string $base,
    ) {
        if (trim($client) === '') {
            throw new \RuntimeException('a portfolio names its client');
        }
        if (trim($base) === '') {
            throw new \RuntimeException('a portfolio names the currency it reports in');
        }
    }

    public function add(Holding $holding): void
    {
        $this->lines[$holding->ticker] = $holding;
    }

    public function count(): int
    {
        return count($this->lines);
    }

    /** @return list<Holding> */
    public function holdings(): array
    {
        $out = array_values($this->lines);
        usort($out, static fn (Holding $a, Holding $b): int => strcmp($a->ticker, $b->ticker));

        return $out;
    }

    public function cost(): float
    {
        $total = 0.0;
        foreach ($this->lines as $holding) {
            $total += $holding->costBasis();
        }

        return round($total, 2);
    }

    public function value(): float
    {
        $total = 0.0;
        foreach ($this->lines as $holding) {
            $total += $holding->value();
        }

        return round($total, 2);
    }

    public function gain(): float
    {
        return round($this->value() - $this->cost(), 2);
    }

    public function gainPercent(): float
    {
        if ($this->cost() === 0.0) {
            return 0.0;
        }

        return round($this->gain() / $this->cost() * 100, 4);
    }

    /** @return array<string, float> */
    public function byKind(): array
    {
        $out = [];
        foreach ($this->lines as $holding) {
            $out[$holding->kind] = round(($out[$holding->kind] ?? 0.0) + $holding->value(), 2);
        }
        ksort($out);

        return $out;
    }

    /** @return array<string, float> */
    public function weights(): array
    {
        $total = $this->value();
        if ($total === 0.0) {
            return [];
        }
        $out = [];
        foreach ($this->byKind() as $kind => $value) {
            $out[$kind] = round($value / $total * 100, 2);
        }

        return $out;
    }
}
