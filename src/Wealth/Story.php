<?php

declare(strict_types=1);

namespace NextWealth\Wealth;

/**
 * One item on the news page: a headline, the publication that ran it, the day
 * it ran, and the market it speaks to. A reader taps the headline and lands
 * on the publisher's own page.
 */
final class Story
{
    public function __construct(
        public readonly string $headline,
        public readonly string $publication,
        public readonly string $day,
        public readonly string $market,
        public readonly string $address,
    ) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) !== 1) {
            throw new \RuntimeException('a story carries the day it ran as YYYY-MM-DD');
        }
        if (trim($headline) === '') {
            throw new \RuntimeException('a story carries a headline');
        }
        if (preg_match('#^https://#', $address) !== 1) {
            throw new \RuntimeException('a story points at an address that starts with https');
        }
    }
}
