<?php

declare(strict_types=1);

namespace NextWealth\Wealth;

/**
 * A legal matter a client brought to the desk. It carries a subject, the kind
 * of work, the day it opened, the day it closed when it has closed, and the
 * lawyer who owns it. The desk tracks the matter and nothing more.
 */
final class Matter
{
    public const OPEN = 'open';
    public const CLOSED = 'closed';

    public function __construct(
        public readonly string $id,
        public readonly string $client,
        public readonly string $kind,
        public readonly string $subject,
        public readonly string $opened,
        public readonly string $lawyer,
        public readonly ?string $closed = null,
    ) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $opened) !== 1) {
            throw new \RuntimeException('a matter opens on a day written as YYYY-MM-DD');
        }
        if ($closed !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $closed) !== 1) {
            throw new \RuntimeException('a matter closes on a day written as YYYY-MM-DD');
        }
        if ($closed !== null && $closed < $opened) {
            throw new \RuntimeException('a matter cannot close before it opened');
        }
        if (trim($subject) === '') {
            throw new \RuntimeException('a matter names its subject');
        }
    }

    public function state(): string
    {
        return $this->closed === null ? self::OPEN : self::CLOSED;
    }

    public function days(): int
    {
        $from = new \DateTimeImmutable($this->opened);
        $to = new \DateTimeImmutable($this->closed ?? 'today');

        return (int) $from->diff($to)->days;
    }
}
