<?php

declare(strict_types=1);

namespace NextWealth\Connect;

use NextWealth\Wealth\Report;

/**
 * The report read through the connector. A manager who runs NEXT WEALTH hands
 * the figures to any other reader of the family through the same layer the
 * other systems use.
 */
final class ReportSource implements Source
{
    public function __construct(private readonly Report $report)
    {
    }

    public function label(): string
    {
        return 'portfolio:report';
    }

    /** @return list<array<string, mixed>> */
    public function lines(): array
    {
        $out = [];
        foreach ($this->report->lines() as $line) {
            $out[] = $line;
        }
        $summary = $this->report->summary();
        $out[] = ['ticker' => 'TOTAL', 'name' => $summary['client'], 'kind' => 'total', 'units' => $summary['holdings'], 'cost' => $summary['cost'], 'value' => $summary['value'], 'gain' => $summary['gain'], 'gain_percent' => $summary['gain_percent']];

        return $out;
    }
}
