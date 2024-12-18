<?php

namespace Marble\JiraKpi\Domain\Model\Unit;

readonly class Day extends Unit
{
    public function toSecond(): Second
    {
        return new Second($this->value * Second::SECONDS_IN_DAY);
    }
}
