<?php

namespace Marble\JiraKpi\Domain\Model\Result;

use Carbon\CarbonImmutable;
use Marble\JiraKpi\Domain\Model\Unit\Second;

readonly class MonthlyCycleTime
{
    public function __construct(
        public CarbonImmutable $month,
        public int             $done,
        public Second          $avgCycleTime,
        public Second          $avgCycleTimeWithoutSlowest,
        public array           $slowest,
    ) {
    }
}
