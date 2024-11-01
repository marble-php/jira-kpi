<?php

namespace Marble\JiraKpi\Domain\Model\Result;

use Carbon\CarbonImmutable;
use Marble\JiraKpi\Domain\Model\Unit\Second;
use function Marble\JiraKpi\Domain\div;

readonly class MonthlyTimePendingRelease
{
    public function __construct(
        public CarbonImmutable $month,
        public int             $released,
        public Second          $pendingRelease,
        public array           $slowest,
    ) {
    }

    public function getAvgTimePendingRelease(): Second
    {
        return new Second(div($this->pendingRelease->value, $this->released));
    }
}
