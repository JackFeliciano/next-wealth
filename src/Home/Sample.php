<?php

declare(strict_types=1);

namespace NextWealth\Home;

use NextWealth\Wealth\Desk;
use NextWealth\Wealth\Holding;
use NextWealth\Wealth\Matter;
use NextWealth\Wealth\Newsroom;
use NextWealth\Wealth\Portfolio;
use NextWealth\Wealth\Report;
use NextWealth\Wealth\Story;

/**
 * One client and one quarter, all invented and belonging to this demo only.
 * The client holds six lines, the desk carries four matters, and the news
 * page points at addresses this demo never fetches.
 */
final class Sample
{
    private Portfolio $portfolio;
    private Desk $desk;
    private Newsroom $newsroom;
    private Report $report;

    public function __construct()
    {
        $this->portfolio = new Portfolio('Halloran Family Office', 'BRL');
        $this->portfolio->add(new Holding('PETR4', 'Petroleo Brasileiro', Holding::EQUITY, 4200.0, 32.10, 38.44));
        $this->portfolio->add(new Holding('VALE3', 'Vale', Holding::EQUITY, 1800.0, 61.40, 58.20));
        $this->portfolio->add(new Holding('ITSA4', 'Itausa', Holding::EQUITY, 9000.0, 9.85, 11.30));
        $this->portfolio->add(new Holding('NTN-B 2035', 'Tesouro IPCA 2035', Holding::BOND, 300.0, 2840.00, 3015.50));
        $this->portfolio->add(new Holding('IMAB11', 'Fundo de renda fixa', Holding::FUND, 1200.0, 96.30, 99.10));
        $this->portfolio->add(new Holding('CASH-BRL', 'Conta corrente', Holding::CASH, 1.0, 148000.00, 148000.00));

        $this->report = new Report($this->portfolio, '2026 Q1', 7420.55);

        $this->desk = new Desk();
        $this->desk->open(new Matter('m-1', 'Halloran Family Office', 'Succession', 'Holding structure for the second generation', '2025-11-03', 'Dra. Vasconcelos'));
        $this->desk->open(new Matter('m-2', 'Halloran Family Office', 'Tax', 'Treatment of the offshore fund income', '2026-01-14', 'Dr. Bittencourt', '2026-03-02'));
        $this->desk->open(new Matter('m-3', 'Halloran Family Office', 'Property', 'Purchase of the Rio apartment', '2026-02-09', 'Dra. Vasconcelos'));
        $this->desk->open(new Matter('m-4', 'Ridgeway Partners', 'Tax', 'Transfer pricing review', '2026-03-18', 'Dr. Bittencourt'));

        $this->newsroom = new Newsroom();
        $this->newsroom->carry(new Story('Central bank holds the rate for a fourth meeting', 'Valor Economico', '2026-03-19', 'Brazil', 'https://www.valor.com.br/'));
        $this->newsroom->carry(new Story('Treasury publishes the new bond calendar', 'Tesouro Nacional', '2026-03-12', 'Brazil', 'https://www.tesouro.gov.br/'));
        $this->newsroom->carry(new Story('Fund managers cut the cash weight in March', 'Financial Times', '2026-03-27', 'Global', 'https://www.ft.com/'));
    }

    public function portfolio(): Portfolio
    {
        return $this->portfolio;
    }

    public function report(): Report
    {
        return $this->report;
    }

    public function desk(): Desk
    {
        return $this->desk;
    }

    public function newsroom(): Newsroom
    {
        return $this->newsroom;
    }
}
