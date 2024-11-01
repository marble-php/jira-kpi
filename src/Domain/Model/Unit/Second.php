<?php

namespace Marble\JiraKpi\Domain\Model\Unit;

readonly class Second extends Unit
{
    public const int SECONDS_IN_DAY = 60 * 60 * 24;

    public function toDay(): Day
    {
        return new Day($this->value / self::SECONDS_IN_DAY);
    }
}
