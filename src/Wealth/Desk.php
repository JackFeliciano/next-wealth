<?php

declare(strict_types=1);

namespace NextWealth\Wealth;

/**
 * The legal desk. It holds the matters of every client, counts what sits open,
 * and lists what one client has brought.
 */
final class Desk
{
    /** @var array<string, Matter> */
    private array $matters = [];

    public function open(Matter $matter): void
    {
        if (isset($this->matters[$matter->id])) {
            throw new \RuntimeException('a matter id is used once');
        }
        $this->matters[$matter->id] = $matter;
    }

    public function count(): int
    {
        return count($this->matters);
    }

    /** @return list<Matter> */
    public function all(): array
    {
        $out = array_values($this->matters);
        usort($out, static fn (Matter $a, Matter $b): int => strcmp($a->id, $b->id));

        return $out;
    }

    /** @return list<Matter> */
    public function forClient(string $client): array
    {
        $out = [];
        foreach ($this->all() as $matter) {
            if ($matter->client === $client) {
                $out[] = $matter;
            }
        }

        return $out;
    }

    /** @return list<Matter> */
    public function openMatters(): array
    {
        $out = [];
        foreach ($this->all() as $matter) {
            if ($matter->state() === Matter::OPEN) {
                $out[] = $matter;
            }
        }

        return $out;
    }

    /** @return array<string, int> */
    public function byKind(): array
    {
        $out = [];
        foreach ($this->all() as $matter) {
            $out[$matter->kind] = ($out[$matter->kind] ?? 0) + 1;
        }
        ksort($out);

        return $out;
    }
}
