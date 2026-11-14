<?php

declare(strict_types=1);

namespace NextWealth\Tests;

use NextWealth\Wealth\Calculator;
use NextWealth\Wealth\Holding;
use NextWealth\Wealth\Portfolio;

$book = new Portfolio('Halloran Family Office', 'BRL');
$book->add(new Holding('PETR4', 'Petroleo Brasileiro', Holding::EQUITY, 100.0, 30.0, 38.0));
$book->add(new Holding('VALE3', 'Vale', Holding::EQUITY, 50.0, 60.0, 58.0));
$book->add(new Holding('CASH-BRL', 'Conta corrente', Holding::CASH, 1.0, 1000.0, 1000.0));

return [
    ['a holding costs and values its units', static function (): void {
        $holding = new Holding('PETR4', 'Petroleo Brasileiro', Holding::EQUITY, 10.0, 30.0, 38.0);
        Assert::same(300.0, $holding->costBasis(), 'the cost basis drifted');
        Assert::same(380.0, $holding->value(), 'the value drifted');
        Assert::same(80.0, $holding->gain(), 'the gain drifted');
        Assert::same(26.6667, $holding->gainPercent(), 'the gain percent drifted');
    }],
    ['a holding sits on more than zero units', static function (): void {
        try {
            new Holding('PETR4', 'Petroleo', Holding::EQUITY, 0.0, 30.0, 38.0);
            Assert::true(false, 'a holding of zero units was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('zero units', $e->getMessage());
        }
    }],
    ['a holding is one of four kinds', static function (): void {
        try {
            new Holding('XYZ', 'Unknown', 'derivative', 1.0, 1.0, 1.0);
            Assert::true(false, 'a fifth kind was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('equity', $e->getMessage());
        }
    }],
    ['a holding names its ticker', static function (): void {
        try {
            new Holding('', 'Nobody', Holding::EQUITY, 1.0, 1.0, 1.0);
            Assert::true(false, 'a holding with no ticker was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('ticker', $e->getMessage());
        }
    }],
    ['the portfolio joins its lines', static function () use ($book): void {
        Assert::same(3, $book->count(), 'the line count drifted');
        Assert::same(7000.0, $book->cost(), 'the cost drifted');
        Assert::same(7700.0, $book->value(), 'the value drifted');
        Assert::same(700.0, $book->gain(), 'the gain drifted');
        Assert::same(10.0, $book->gainPercent(), 'the gain percent drifted');
    }],
    ['the portfolio orders its lines by ticker', static function () use ($book): void {
        $tickers = [];
        foreach ($book->holdings() as $holding) {
            $tickers[] = $holding->ticker;
        }
        Assert::same(['CASH-BRL', 'PETR4', 'VALE3'], $tickers, 'the lines came back unsorted');
    }],
    ['the portfolio splits its value by kind', static function () use ($book): void {
        Assert::same(['cash' => 1000.0, 'equity' => 6700.0], $book->byKind(), 'the split drifted');
    }],
    ['the portfolio weighs each kind', static function () use ($book): void {
        $weights = $book->weights();
        Assert::same(12.99, $weights['cash'], 'the cash weight drifted');
        Assert::same(87.01, $weights['equity'], 'the equity weight drifted');
    }],
    ['a portfolio names its client and its currency', static function (): void {
        try {
            new Portfolio('', 'BRL');
            Assert::true(false, 'a portfolio with no client was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('client', $e->getMessage());
        }
    }],
    ['compound interest holds the interest in the account', static function (): void {
        Assert::same(179084.77, Calculator::compound(100000.0, 6.0, 10), 'the compound figure drifted');
    }],
    ['a sinking fund divides the target when the rate is zero', static function (): void {
        Assert::same(50000.0, Calculator::sinkingFund(1000000.0, 0.0, 20), 'the zero rate case drifted');
        Assert::same(30242.59, Calculator::sinkingFund(1000000.0, 5.0, 20), 'the sinking fund figure drifted');
    }],
    ['a sinking fund runs one year or more', static function (): void {
        try {
            Calculator::sinkingFund(1000.0, 5.0, 0);
            Assert::true(false, 'a zero term was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('one year', $e->getMessage());
        }
    }],
    ['a drawdown reads the rate against the book', static function (): void {
        Assert::same(308.0, Calculator::drawdown(7700.0, 4.0), 'the drawdown drifted');
    }],
    ['a total return carries the income', static function (): void {
        Assert::same(15.7143, Calculator::totalReturn(7000.0, 7700.0, 400.0), 'the total return drifted');
    }],
    ['a total return needs a starting value above zero', static function (): void {
        try {
            Calculator::totalReturn(0.0, 100.0, 0.0);
            Assert::true(false, 'a zero starting value was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('above zero', $e->getMessage());
        }
    }],
    ['a share reads one part against the total', static function (): void {
        Assert::same(25.0, Calculator::share(250.0, 1000.0), 'the share drifted');
    }],
];
