<?php

declare(strict_types=1);

namespace NextWealth\Tests;

use NextWealth\Wealth\Holding;
use NextWealth\Wealth\Portfolio;
use NextWealth\Wealth\Report;

$book = new Portfolio('Halloran Family Office', 'BRL');
$book->add(new Holding('PETR4', 'Petroleo Brasileiro', Holding::EQUITY, 100.0, 30.0, 38.0));
$book->add(new Holding('VALE3', 'Vale', Holding::EQUITY, 50.0, 60.0, 58.0));
$book->add(new Holding('CASH-BRL', 'Conta corrente', Holding::CASH, 1.0, 1000.0, 1000.0));
$report = new Report($book, '2026 Q1', 400.0);

return [
    ['the report names the client, the currency and the period', static function () use ($report): void {
        $summary = $report->summary();
        Assert::same('Halloran Family Office', $summary['client'], 'the client drifted');
        Assert::same('BRL', $summary['base'], 'the currency drifted');
        Assert::same('2026 Q1', $summary['period'], 'the period drifted');
    }],
    ['the report carries the figures of the portfolio', static function () use ($report): void {
        $summary = $report->summary();
        Assert::same(7000.0, $summary['cost'], 'the cost drifted');
        Assert::same(7700.0, $summary['value'], 'the value drifted');
        Assert::same(700.0, $summary['gain'], 'the gain drifted');
        Assert::same(400.0, $summary['income'], 'the income drifted');
        Assert::same(3, $summary['holdings'], 'the holding count drifted');
    }],
    ['the report writes one line per holding', static function () use ($report): void {
        $lines = $report->lines();
        Assert::same(3, count($lines), 'the line count drifted');
        Assert::same('CASH-BRL', $lines[0]['ticker'], 'the lines came back unsorted');
        Assert::same(3800.0, $lines[1]['value'], 'the line value drifted');
        Assert::same(26.6667, $lines[1]['gain_percent'], 'the line gain drifted');
    }],
    ['the report says which way the book moved', static function () use ($report): void {
        Assert::same('up', $report->direction(), 'the direction drifted');
        $flat = new Report(new Portfolio('Quiet Client', 'BRL'), '2026 Q1');
        Assert::same('flat', $flat->direction(), 'an empty book read as a gain');
    }],
    ['the report weighs the kinds it holds', static function () use ($report): void {
        $weights = $report->weights();
        Assert::same(12.99, $weights['cash'], 'the cash weight drifted');
    }],
    ['a report names its period', static function (): void {
        try {
            new Report(new Portfolio('Quiet Client', 'BRL'), '');
            Assert::true(false, 'a report with no period was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('period', $e->getMessage());
        }
    }],
    ['income is zero or more', static function (): void {
        try {
            new Report(new Portfolio('Quiet Client', 'BRL'), '2026 Q1', -1.0);
            Assert::true(false, 'a negative income was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('zero or more', $e->getMessage());
        }
    }],
];
