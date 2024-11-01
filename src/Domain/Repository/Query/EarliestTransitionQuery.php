<?php

namespace Marble\JiraKpi\Domain\Repository\Query;

use Marble\JiraKpi\Domain\Model\Issue\Issue;
use Marble\JiraKpi\Domain\Model\Issue\IssueStatus;

readonly class EarliestTransitionQuery
{
    public function __construct(
        public Issue       $issue,
        public IssueStatus $to,
    ) {
    }
}
