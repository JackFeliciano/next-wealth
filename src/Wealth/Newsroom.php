<?php

declare(strict_types=1);

namespace NextWealth\Wealth;

/**
 * The news page. Every item carries the address of the publication that ran
 * it, so nothing is reprinted here and the reader always reaches the source.
 */
final class Newsroom
{
    /** @var list<Story> */
    private array $stories = [];

    public function carry(Story $story): void
    {
        $this->stories[] = $story;
    }

    /** @return list<Story> */
    public function all(): array
    {
        $out = $this->stories;
        usort($out, static fn (Story $a, Story $b): int => strcmp($b->day, $a->day));

        return $out;
    }

    /** @return list<Story> */
    public function forMarket(string $market): array
    {
        $out = [];
        foreach ($this->all() as $story) {
            if ($story->market === $market) {
                $out[] = $story;
            }
        }

        return $out;
    }

    /** @return list<string> */
    public function markets(): array
    {
        $seen = [];
        foreach ($this->stories as $story) {
            $seen[$story->market] = true;
        }
        $out = array_keys($seen);
        sort($out);

        return $out;
    }
}
