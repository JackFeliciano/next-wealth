<?php

declare(strict_types=1);

namespace NextWealth\Wealth;

/**
 * One line of a client's portfolio: what the client holds, how many units,
 * what one unit cost on the day it was bought, and what one unit is worth on
 * the day the report runs.
 */
final class Holding
{
    public const EQUITY = 'equity';
    public const BOND = 'bond';
    public const FUND = 'fund';
    public const CASH = 'cash';

    public function __construct(
        public readonly string $ticker,
        public readonly string $name,
        public readonly string $kind,
        public readonly float $units,
        public readonly float $cost,
        public readonly float $price,
    ) {
        if (trim($ticker) === '') {
            throw new \RuntimeException('a holding carries its ticker');
        }
        if (!in_array($kind, [self::EQUITY, self::BOND, self::FUND, self::CASH], true)) {
            throw new \RuntimeException('a holding is equity, bond, fund or cash');
        }
        if ($units <= 0.0) {
            throw new \RuntimeException('a holding sits on more than zero units');
        }
        if ($cost < 0.0 || $price < 0.0) {
            throw new \RuntimeException('a price is zero or more');
        }
    }

    public function costBasis(): float
    {
        return round($this->units * $this->cost, 2);
    }

    public function value(): float
    {
        return round($this->units * $this->price, 2);
    }

    public function gain(): float
    {
        return round($this->value() - $this->costBasis(), 2);
    }

    public function gainPercent(): float
    {
        if ($this->costBasis() === 0.0) {
            return 0.0;
        }

        return round($this->gain() / $this->costBasis() * 100, 4);
    }
}
