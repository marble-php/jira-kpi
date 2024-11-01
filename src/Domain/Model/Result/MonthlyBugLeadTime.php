<?php

namespace Marble\JiraKpi\Domain\Model\Result;

use Carbon\CarbonImmutable;
use Marble\JiraKpi\Domain\Model\Unit\Second;

readonly class MonthlyBugLeadTime
{
    public function __construct(
        public CarbonImmutable $month,
        public int             $fixed,
        public Second          $avgLeadTime,
        public Second          $avgLeadTimeMax6Months,
        public Second          $avgLeadTimeMax2Months,
        public array           $slowest,
    ) {
    }
}
