<?php

namespace Marble\JiraKpi\Domain\Repository\Query;

use Marble\JiraKpi\Domain\Model\Issue\Issue;
use Marble\JiraKpi\Domain\Model\Issue\IssueStatus;

readonly class LatestTransitionQuery
{
    public function __construct(
        public Issue       $issue,
        public IssueStatus $to,
    ) {
    }
}
