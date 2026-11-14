<?php

declare(strict_types=1);

namespace NextWealth\Wealth;

/**
 * The calculators a wealth manager runs with a client in the room. Every one
 * takes plain figures and hands back one plain figure.
 */
final class Calculator
{
    /**
     * What a sum becomes after a number of years at a yearly rate, when the
     * interest stays in the account.
     */
    public static function compound(float $principal, float $rate, int $years): float
    {
        if ($principal < 0.0) {
            throw new \RuntimeException('a principal is zero or more');
        }
        if ($years < 0) {
            throw new \RuntimeException('a term runs zero years or more');
        }

        return round($principal * (1 + $rate / 100) ** $years, 2);
    }

    /**
     * The sum a client must set aside each year to reach a target when the
     * interest stays in the account. A zero rate divides the target by the term.
     */
    public static function sinkingFund(float $target, float $rate, int $years): float
    {
        if ($years <= 0) {
            throw new \RuntimeException('a sinking fund runs one year or more');
        }
        if ($rate == 0.0) {
            return round($target / $years, 2);
        }
        $factor = (1 + $rate / 100) ** $years - 1;

        return round($target * ($rate / 100) / $factor, 2);
    }

    /**
     * The yearly sum a portfolio pays out at a stated withdrawal rate.
     */
    public static function drawdown(float $value, float $rate): float
    {
        if ($value < 0.0) {
            throw new \RuntimeException('a portfolio value is zero or more');
        }
        if ($rate < 0.0) {
            throw new \RuntimeException('a withdrawal rate is zero or more');
        }

        return round($value * $rate / 100, 2);
    }

    /**
     * The yearly return a portfolio delivered, given what it started with,
     * what it holds now, and what it paid out along the way.
     */
    public static function totalReturn(float $start, float $end, float $income): float
    {
        if ($start <= 0.0) {
            throw new \RuntimeException('a starting value sits above zero');
        }

        return round(($end + $income - $start) / $start * 100, 4);
    }

    /**
     * The share of a portfolio that sits in one class, as a percentage.
     */
    public static function share(float $part, float $total): float
    {
        if ($total <= 0.0) {
            throw new \RuntimeException('a total sits above zero');
        }

        return round($part / $total * 100, 2);
    }
}
