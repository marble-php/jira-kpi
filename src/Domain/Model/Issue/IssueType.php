<?php

namespace Marble\JiraKpi\Domain\Model\Issue;

enum IssueType
{
    case STORY;
    case BUG;
    case IMPROVEMENT;
    case TASK;
    case EPIC;
    case SUBTASK;

    public function hasUsefulActiveTime(): bool
    {
        return $this === self::STORY
            || $this === self::BUG
            || $this === self::IMPROVEMENT
            || $this === self::SUBTASK;
    }
}
