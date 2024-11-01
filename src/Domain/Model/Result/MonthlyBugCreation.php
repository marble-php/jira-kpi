<?php

namespace Marble\JiraKpi\Domain\Model\Result;

use Carbon\CarbonImmutable;
use Marble\JiraKpi\Domain\Model\Unit\StoryPoint;
use function Marble\JiraKpi\Domain\div;

readonly class MonthlyBugCreation
{
    public function __construct(
        public CarbonImmutable $month,
        public int             $created,
        public int             $estimated,
        public StoryPoint      $storyPoints,
    ) {
    }

    public function getEstimatedFraction(): float
    {
        return div($this->estimated, $this->created);
    }

    public function getAvgStoryPointEstimate(): float
    {
        return div($this->storyPoints->value, $this->estimated);
    }
}
