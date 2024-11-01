<?php

namespace Marble\JiraKpi\Domain\Model\Result;

use Carbon\CarbonImmutable;
use Marble\JiraKpi\Domain\Model\Unit\Second;
use function Marble\JiraKpi\Domain\div;

readonly class MonthlyBugFixingTime
{
    public function __construct(
        public CarbonImmutable $month,
        public Second          $workingOnAny,
        public Second          $fixingBugs,
        public array           $slowest,
    ) {
    }

    public function getFractionFixingBugs(): float
    {
        return div($this->fixingBugs->value, $this->workingOnAny->value);
    }
}
