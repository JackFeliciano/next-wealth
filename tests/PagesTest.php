<?php

declare(strict_types=1);

namespace NextWealth\Tests;

use NextWealth\App\Front;
use NextWealth\App\Router;
use NextWealth\App\Views;

$router = new Router('/nextwealth/');
$front = new Front(new Views('/nextwealth/'));
$page = $front->handle('/nextwealth/', $router);
$status = $front->handle('/nextwealth/status', $router);
$missing = $front->handle('/nextwealth/nowhere', $router);

return [
    ['the router names the front page', static function () use ($router): void {
        Assert::same('front', $router->route('/nextwealth/'), 'the front route drifted');
    }],
    ['the router names the status page', static function () use ($router): void {
        Assert::same('status', $router->route('/nextwealth/status'), 'the status route drifted');
    }],
    ['the router names an unknown path', static function () use ($router): void {
        Assert::same('missing', $router->route('/nextwealth/nowhere'), 'the missing route drifted');
    }],
    ['the router strips its base once', static function () use ($router): void {
        Assert::same('/', $router->strip('/nextwealth/'), 'the stripped path drifted');
        Assert::same('/status', $router->strip('/nextwealth/status'), 'the stripped path drifted');
    }],
    ['the front page carries the product name', static function () use ($page): void {
        Assert::contains('<h1>NEXT WEALTH</h1>', $page);
    }],
    ['the front page prints the portfolio', static function () use ($page): void {
        Assert::contains('The portfolio', $page);
        Assert::contains('PETR4', $page);
        Assert::contains('Halloran Family Office', $page);
    }],
    ['the front page prints the shape of the book', static function () use ($page): void {
        Assert::contains('Shape of the book', $page);
    }],
    ['the front page prints the calculators', static function () use ($page): void {
        Assert::contains('The calculators', $page);
    }],
    ['the front page prints the legal desk', static function () use ($page): void {
        Assert::contains('The legal desk', $page);
        Assert::contains('Succession', $page);
    }],
    ['the front page prints the news', static function () use ($page): void {
        Assert::contains('The news', $page);
        Assert::contains('Valor Economico', $page);
    }],
    ['the front page prints the report', static function () use ($page): void {
        Assert::contains('The report', $page);
        Assert::contains('2026 Q1', $page);
    }],
    ['the status page reports the book', static function () use ($status): void {
        Assert::contains('holdings', $status);
        Assert::contains('matters open', $status);
    }],
    ['an unknown path answers as not found', static function () use ($missing): void {
        Assert::contains('Nothing answers at', $missing);
    }],
];
