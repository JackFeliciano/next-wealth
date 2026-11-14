<?php

declare(strict_types=1);

namespace NextWealth\App;

use NextWealth\Home\Sample;

/**
 * Every page the showroom prints. The markup is written by hand so a reader
 * can follow one figure from the portfolio to the report page.
 */
final class Views
{
    public function __construct(private readonly string $base)
    {
    }

    private function url(string $path): string
    {
        $base = '/' . trim($this->base, '/');

        return ($base === '/' ? '' : $base) . $path;
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function money(float $value, string $base): string
    {
        return $base . ' ' . number_format($value, 2, '.', ',');
    }

    private function table(array $head, array $rows): string
    {
        $html = '<table><thead><tr>';
        foreach ($head as $cell) {
            $html .= '<th>' . $this->e($cell) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . $this->e((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }

        return $html . '</tbody></table>';
    }

    private function head(string $title): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>' . $this->e($title) . '</title>
<link rel="stylesheet" href="' . $this->e($this->url('/assets/site.css')) . '">
</head>
<body>
<header>
<a class="brand" href="' . $this->e($this->url('/')) . '">NEXT WEALTH</a>
<nav>
<a href="' . $this->e($this->url('/')) . '">Overview</a>
<a href="' . $this->e($this->url('/status')) . '">Status</a>
</nav>
</header>
<main>
';
    }

    private function foot(): string
    {
        return "</main>
</body>
</html>
";
    }

    public function front(Sample $sample): string
    {
        $portfolio = $sample->portfolio();
        $report = $sample->report();
        $summary = $report->summary();
        $base = $portfolio->base;

        $html = $this->head('NEXT WEALTH');
        $html .= '<h1>NEXT WEALTH</h1>';
        $html .= '<p class="lede">A wealth manager keeps a client portfolio, runs the calculators with the client, '
            . 'tracks the legal matters on the desk, reads the market news, and hands over a report.</p>';

        $html .= '<h2>The portfolio</h2>';
        $html .= '<p class="figure">' . $this->e($portfolio->client . ' holds ' . $summary['holdings']
            . ' lines worth ' . $this->money($summary['value'], $base) . ' against a cost of '
            . $this->money($summary['cost'], $base) . '.') . '</p>';

        $rows = [];
        foreach ($report->lines() as $line) {
            $rows[] = [
                $line['ticker'],
                $line['name'],
                $line['kind'],
                $line['units'],
                $this->money((float) $line['cost'], $base),
                $this->money((float) $line['value'], $base),
                $this->money((float) $line['gain'], $base),
                $line['gain_percent'] . '%',
            ];
        }
        $html .= $this->table(['Ticker', 'Name', 'Kind', 'Units', 'Cost', 'Value', 'Gain', 'Gain %'], $rows);

        $html .= '<h2>Shape of the book</h2>';
        $rows = [];
        foreach ($report->weights() as $kind => $weight) {
            $rows[] = [$kind, $this->money($portfolio->byKind()[$kind], $base), $weight . '%'];
        }
        $html .= $this->table(['Kind', 'Value', 'Weight'], $rows);

        $html .= '<h2>The calculators</h2>';
        $rows = [];
        $rows[] = ['Compound, 100,000 at 6% for 10 years', $this->money(\NextWealth\Wealth\Calculator::compound(100000.0, 6.0, 10), $base)];
        $rows[] = ['Yearly sum to reach 1,000,000 in 20 years at 5%', $this->money(\NextWealth\Wealth\Calculator::sinkingFund(1000000.0, 5.0, 20), $base)];
        $rows[] = ['Yearly drawdown at 4% of the book', $this->money(\NextWealth\Wealth\Calculator::drawdown($summary['value'], 4.0), $base)];
        $rows[] = ['Total return over the period', \NextWealth\Wealth\Calculator::totalReturn((float) $summary['cost'], (float) $summary['value'], (float) $summary['income']) . '%'];
        $html .= $this->table(['Calculator', 'Result'], $rows);

        $html .= '<h2>The legal desk</h2>';
        $rows = [];
        foreach ($sample->desk()->all() as $matter) {
            $rows[] = [$matter->id, $matter->client, $matter->kind, $matter->subject, $matter->lawyer, $matter->state(), $matter->days()];
        }
        $html .= $this->table(['Id', 'Client', 'Kind', 'Subject', 'Lawyer', 'State', 'Days'], $rows);

        $html .= '<h2>The news</h2>';
        $rows = [];
        foreach ($sample->newsroom()->all() as $story) {
            $rows[] = [$story->day, $story->headline, $story->publication, $story->market];
        }
        $html .= $this->table(['Day', 'Headline', 'Publication', 'Market'], $rows);

        $html .= '<h2>The report</h2>';
        $rows = [];
        foreach ($summary as $key => $value) {
            $rows[] = [$key, is_float($value) ? $this->money($value, $base) : (string) $value];
        }
        $rows[] = ['direction', $report->direction()];
        $html .= $this->table(['Field', 'Value'], $rows);

        return $html . $this->foot();
    }

    public function status(Sample $sample): string
    {
        $html = $this->head('NEXT WEALTH status');
        $html .= '<h1>Status</h1>';
        $html .= $this->table(['Measure', 'Value'], [
            ['holdings', $sample->portfolio()->count()],
            ['portfolio value', $sample->portfolio()->base . ' ' . number_format($sample->portfolio()->value(), 2, '.', ',')],
            ['matters', $sample->desk()->count()],
            ['matters open', count($sample->desk()->openMatters())],
            ['stories', count($sample->newsroom()->all())],
            ['markets', count($sample->newsroom()->markets())],
        ]);

        return $html . $this->foot();
    }

    public function notFound(string $path): string
    {
        $html = $this->head('Not found');
        $html .= '<h1>Not found</h1>';
        $html .= '<p class="figure">Nothing answers at ' . $this->e($path) . '.</p>';

        return $html . $this->foot();
    }
}
