# NEXT WEALTH

A practical set of Wealth Management tools for wealth managers and their clients: portfolio management, calculators, legal support, news, and reports.

## What NEXT WEALTH is

NEXT WEALTH holds the figures and the records for wealth managers and their clients. The tools cover the portfolio, the calculators, the legal desk, the news and the report. The system gives no advice and signs no recommendation. It publishes no data of its own.

## The portfolio

Each line in the portfolio carries a ticker, a name, the kind of asset, the units, what one unit cost and what one unit is worth now. The kinds of asset are equity, bond, fund and cash. The portfolio joins the lines into a cost, a value, a gain and a gain percentage. It splits the value by kind.

## The calculators

The system runs these calculators:

- compound growth
- the yearly sum that reaches a target
- the yearly drawdown at a stated rate
- the total return over a period with the income included
- the share of a book that sits in one class

## The legal desk

Each matter carries a client, a kind of work, a subject, the day it opened, the day it closed and the lawyer who owns it. The desk counts what sits open and lists what one client brought.

## The news

Each item names the publication that ran it, the day, the market and the address of the publisher. The reader taps through to the source. NEXT WEALTH reprints nothing.

## The report

The report states the period, the client and the currency. It gives the cost, the value, the gain and the income. It writes one line per holding. It gives the weights of the asset kinds.

## The connector

The connector layer hands every report line to the rest of the family.

## Running it

NEXT WEALTH runs on PHP 8.2 or later. The system needs no Composer and no vendor directory. Set NEXTWEALTH_BASE to /nextwealth/.

Start the server:

```
php -S 127.0.0.1:8124 -t showroom showroom/index.php
```

## The tests

Run the tests:

```
php tests/run.php
```
