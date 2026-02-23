<?php

namespace CodeIgniter\I18n;

use Carbon\Carbon;

class Time
{
    public function __construct(private Carbon $time)
    {
    }

    public static function createFromFormat(string $format, string $time): self
    {
        return new self(Carbon::createFromFormat($format, $time));
    }

    public static function today(): self
    {
        return new self(Carbon::today());
    }

    public function setYear(int $year): self
    {
        $copy = $this->time->copy()->year($year);
        return new self($copy);
    }

    public function difference(self $other): object
    {
        $days = $this->time->diffInDays($other->time, false);
        return new class($days) {
            public function __construct(private int $days)
            {
            }

            public function getDays(): int
            {
                return abs($this->days);
            }
        };
    }

    public function isBefore(self $other): bool
    {
        return $this->time->lt($other->time);
    }
}
