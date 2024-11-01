<?php

namespace Marble\JiraKpi\Domain\Model\Issue;

use Carbon\CarbonImmutable;
use Marble\JiraKpi\Domain\Model\Unit\Second;

readonly class Timeslot
{
    public function __construct(
        public Issue            $issue,
        public IssueStatus      $status,
        public CarbonImmutable  $from,
        public ?CarbonImmutable $to,
    ) {
    }

    public function getDuration(): Second
    {
        return new Second($this->from->diffInWeekdaySeconds($this->to));
    }

    public function getDurationBetween(CarbonImmutable $start, CarbonImmutable $end): Second
    {
        $from = $start->max($this->from);
        $to   = $end->min($this->to);

        return new Second($from->diffInWeekdaySeconds($to));
    }
}
