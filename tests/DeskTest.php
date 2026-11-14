<?php

declare(strict_types=1);

namespace NextWealth\Tests;

use NextWealth\Wealth\Desk;
use NextWealth\Wealth\Matter;
use NextWealth\Wealth\Newsroom;
use NextWealth\Wealth\Story;

$desk = new Desk();
$desk->open(new Matter('m-1', 'Halloran Family Office', 'Succession', 'Holding structure', '2025-11-03', 'Dra. Vasconcelos'));
$desk->open(new Matter('m-2', 'Halloran Family Office', 'Tax', 'Offshore fund income', '2026-01-14', 'Dr. Bittencourt', '2026-03-02'));
$desk->open(new Matter('m-3', 'Ridgeway Partners', 'Tax', 'Transfer pricing', '2026-03-18', 'Dr. Bittencourt'));

$news = new Newsroom();
$news->carry(new Story('Central bank holds the rate', 'Valor Economico', '2026-03-19', 'Brazil', 'https://www.valor.com.br/'));
$news->carry(new Story('Bond calendar published', 'Tesouro Nacional', '2026-03-12', 'Brazil', 'https://www.tesouro.gov.br/'));
$news->carry(new Story('Managers cut the cash weight', 'Financial Times', '2026-03-27', 'Global', 'https://www.ft.com/'));

return [
    ['a matter reads its own state', static function (): void {
        $open = new Matter('m-9', 'Ridgeway Partners', 'Tax', 'Review', '2026-01-05', 'Dr. Bittencourt');
        Assert::same(Matter::OPEN, $open->state(), 'an open matter read closed');
        $closed = new Matter('m-8', 'Ridgeway Partners', 'Tax', 'Review', '2026-01-05', 'Dr. Bittencourt', '2026-02-05');
        Assert::same(Matter::CLOSED, $closed->state(), 'a closed matter read open');
    }],
    ['a closed matter counts the days it ran', static function (): void {
        $closed = new Matter('m-8', 'Ridgeway Partners', 'Tax', 'Review', '2026-01-05', 'Dr. Bittencourt', '2026-02-04');
        Assert::same(30, $closed->days(), 'the day count drifted');
    }],
    ['a matter cannot close before it opened', static function (): void {
        try {
            new Matter('m-7', 'Ridgeway Partners', 'Tax', 'Review', '2026-02-05', 'Dr. Bittencourt', '2026-01-05');
            Assert::true(false, 'a matter closed before it opened');
        } catch (\RuntimeException $e) {
            Assert::contains('before it opened', $e->getMessage());
        }
    }],
    ['a matter names its subject', static function (): void {
        try {
            new Matter('m-6', 'Ridgeway Partners', 'Tax', '', '2026-01-05', 'Dr. Bittencourt');
            Assert::true(false, 'a matter with no subject was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('subject', $e->getMessage());
        }
    }],
    ['the desk counts its matters', static function () use ($desk): void {
        Assert::same(3, $desk->count(), 'the matter count drifted');
    }],
    ['the desk lists the matters of one client', static function () use ($desk): void {
        Assert::same(2, count($desk->forClient('Halloran Family Office')), 'the client filter drifted');
        Assert::same(1, count($desk->forClient('Ridgeway Partners')), 'the client filter drifted');
        Assert::same(0, count($desk->forClient('Nobody')), 'an unknown client carried matters');
    }],
    ['the desk lists what sits open', static function () use ($desk): void {
        $open = $desk->openMatters();
        Assert::same(2, count($open), 'the open count drifted');
        Assert::same('m-1', $open[0]->id, 'the open list came back wrong');
    }],
    ['the desk groups its matters by kind', static function () use ($desk): void {
        Assert::same(['Succession' => 1, 'Tax' => 2], $desk->byKind(), 'the kind count drifted');
    }],
    ['a matter id is used once', static function () use ($desk): void {
        try {
            $desk->open(new Matter('m-1', 'Ridgeway Partners', 'Tax', 'Second', '2026-01-05', 'Dr. Bittencourt'));
            Assert::true(false, 'a duplicate matter id was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('used once', $e->getMessage());
        }
    }],
    ['a story points at an https address', static function (): void {
        try {
            new Story('Headline', 'Publication', '2026-03-19', 'Brazil', 'http://insecure.example/');
            Assert::true(false, 'a plain http address was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('https', $e->getMessage());
        }
    }],
    ['a story carries a headline and a day', static function (): void {
        try {
            new Story('', 'Publication', '2026-03-19', 'Brazil', 'https://example.com/');
            Assert::true(false, 'a story with no headline was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('headline', $e->getMessage());
        }
    }],
    ['the newsroom carries the newest story first', static function () use ($news): void {
        $all = $news->all();
        Assert::same(3, count($all), 'the story count drifted');
        Assert::same('2026-03-27', $all[0]->day, 'the newest story drifted');
        Assert::same('2026-03-12', $all[2]->day, 'the oldest story drifted');
    }],
    ['the newsroom filters by market', static function () use ($news): void {
        Assert::same(2, count($news->forMarket('Brazil')), 'the market filter drifted');
        Assert::same(1, count($news->forMarket('Global')), 'the market filter drifted');
    }],
    ['the newsroom lists its markets', static function () use ($news): void {
        Assert::same(['Brazil', 'Global'], $news->markets(), 'the markets came back wrong');
    }],
];
