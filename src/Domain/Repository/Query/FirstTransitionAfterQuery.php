<?php

namespace Marble\JiraKpi\Domain\Repository\Query;

use Carbon\CarbonImmutable;

readonly class FirstTransitionAfterQuery
{
    public function __construct(
        public CarbonImmutable $after,
    ) {
    }
}
