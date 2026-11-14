<?php

declare(strict_types=1);

namespace NextWealth\Wealth;

/**
 * The report a manager hands the client. It takes a portfolio, a period and
 * the income the portfolio paid over that period, and writes one page of
 * sections: what the client holds, what it is worth, what it returned, and
 * the legal matters on the desk.
 */
final class Report
{
    public function __construct(
        public readonly Portfolio $portfolio,
        public readonly string $period,
        public readonly float $income = 0.0,
    ) {
        if (trim($period) === '') {
            throw new \RuntimeException('a report names its period');
        }
        if ($income < 0.0) {
            throw new \RuntimeException('income is zero or more');
        }
    }

    /** @return list<array<string, mixed>> */
    public function lines(): array
    {
        $out = [];
        foreach ($this->portfolio->holdings() as $holding) {
            $out[] = [
                'ticker' => $holding->ticker,
                'name' => $holding->name,
                'kind' => $holding->kind,
                'units' => $holding->units,
                'cost' => $holding->costBasis(),
                'value' => $holding->value(),
                'gain' => $holding->gain(),
                'gain_percent' => $holding->gainPercent(),
            ];
        }

        return $out;
    }

    /** @return array<string, mixed> */
    public function summary(): array
    {
        return [
            'client' => $this->portfolio->client,
            'base' => $this->portfolio->base,
            'period' => $this->period,
            'cost' => $this->portfolio->cost(),
            'value' => $this->portfolio->value(),
            'gain' => $this->portfolio->gain(),
            'gain_percent' => $this->portfolio->gainPercent(),
            'income' => round($this->income, 2),
            'holdings' => $this->portfolio->count(),
        ];
    }

    /** @return array<string, float> */
    public function weights(): array
    {
        return $this->portfolio->weights();
    }

    /**
     * The report never claims more than the figures support, so it says in one
     * field whether the book gained or fell over the period.
     */
    public function direction(): string
    {
        $gain = $this->portfolio->gain();
        if ($gain > 0.0) {
            return 'up';
        }
        if ($gain < 0.0) {
            return 'down';
        }

        return 'flat';
    }
}
