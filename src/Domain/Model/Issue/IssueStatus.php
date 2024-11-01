<?php

namespace Marble\JiraKpi\Domain\Model\Issue;

enum IssueStatus
{
    case TO_DO;
    case FEEDBACK_TO_PROCESS;
    case IN_PROGRESS;
    case PENDING_TR;
    case TECH_REVIEW;
    case PENDING_FR;
    case FUNCTIONAL_REVIEW;
    case PENDING_AT;
    case ACCEPTANCE_TESTING;
    case PENDING_RELEASE;
    case DONE;
    case CANCELLED;

    public static function getActiveStatuses(bool $onlyDev = true): array
    {
        $active = [
            self::IN_PROGRESS,
            self::TECH_REVIEW,
            self::FUNCTIONAL_REVIEW,
        ];

        if (!$onlyDev) {
            $active[] = self::ACCEPTANCE_TESTING;
        }

        return $active;
    }

    public function isActive(bool $onlyDev = true): bool
    {
        return $this === self::IN_PROGRESS
            || $this === self::TECH_REVIEW
            || $this === self::FUNCTIONAL_REVIEW
            || ($onlyDev === false && $this === self::ACCEPTANCE_TESTING);
    }
}
