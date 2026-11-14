<?php

declare(strict_types=1);

namespace NextWealth\Tests;

use NextWealth\Connect\Ledger;
use NextWealth\Connect\ReportSource;
use NextWealth\Connect\Source;
use NextWealth\Wealth\Holding;
use NextWealth\Wealth\Portfolio;
use NextWealth\Wealth\Report;

$one = new class implements Source {
    public function label(): string
    {
        return 'portfolio:report';
    }

    public function lines(): array
    {
        return [['ticker' => 'PETR4', 'value' => 380.0]];
    }
};

$two = new class implements Source {
    public function label(): string
    {
        return 'file:prices.json';
    }

    public function lines(): array
    {
        return [['ticker' => 'VALE3', 'value' => 290.0], ['ticker' => 'CASH-BRL', 'value' => 1000.0]];
    }
};

$silent = new class implements Source {
    public function label(): string
    {
        return 'db:prices/2026/q1';
    }

    public function lines(): array
    {
        throw new \RuntimeException('the source did not answer');
    }
};

return [
    ['the ledger counts its sources', static function () use ($one, $two): void {
        $ledger = new Ledger();
        $ledger->add($one);
        $ledger->add($two);
        Assert::same(2, $ledger->count(), 'the source count drifted');
    }],
    ['the ledger names its sources', static function () use ($one, $two): void {
        $ledger = new Ledger();
        $ledger->add($one);
        $ledger->add($two);
        Assert::same(['portfolio:report', 'file:prices.json'], $ledger->labels(), 'the labels drifted');
    }],
    ['the ledger keys a source by its label', static function () use ($one): void {
        $ledger = new Ledger();
        $ledger->add($one);
        $ledger->add($one);
        Assert::same(1, $ledger->count(), 'the same label was counted twice');
    }],
    ['the ledger joins every line of every source', static function () use ($one, $two): void {
        $ledger = new Ledger();
        $ledger->add($one);
        $ledger->add($two);
        Assert::same(3, count($ledger->records()), 'the joined count drifted');
    }],
    ['a source that throws stops the read', static function () use ($one, $silent): void {
        $ledger = new Ledger();
        $ledger->add($one);
        $ledger->add($silent);
        try {
            $ledger->read();
            Assert::true(false, 'a silent source passed the read');
        } catch (\RuntimeException $e) {
            Assert::contains('did not answer', $e->getMessage());
        }
    }],
    ['a read over every source succeeds when each one answers', static function () use ($one, $two): void {
        $ledger = new Ledger();
        $ledger->add($one);
        $ledger->add($two);
        $ledger->read();
        Assert::true(true, 'the read reached every source');
    }],
    ['the report hands its lines to the connector', static function (): void {
        $book = new Portfolio('Halloran Family Office', 'BRL');
        $book->add(new Holding('PETR4', 'Petroleo Brasileiro', Holding::EQUITY, 10.0, 30.0, 38.0));
        $source = new ReportSource(new Report($book, '2026 Q1'));
        Assert::same('portfolio:report', $source->label(), 'the label drifted');
        $lines = $source->lines();
        Assert::same(2, count($lines), 'the line count drifted');
        Assert::same('PETR4', $lines[0]['ticker'], 'the holding line drifted');
        Assert::same('TOTAL', $lines[1]['ticker'], 'the total line drifted');
        Assert::same(380.0, $lines[1]['value'], 'the total value drifted');
    }],
    ['the connector reads the report through the ledger', static function (): void {
        $book = new Portfolio('Halloran Family Office', 'BRL');
        $book->add(new Holding('PETR4', 'Petroleo Brasileiro', Holding::EQUITY, 10.0, 30.0, 38.0));
        $book->add(new Holding('VALE3', 'Vale', Holding::EQUITY, 10.0, 60.0, 58.0));
        $ledger = new Ledger();
        $ledger->add(new ReportSource(new Report($book, '2026 Q1')));
        Assert::same(3, count($ledger->records()), 'the report lines drifted');
    }],
];
