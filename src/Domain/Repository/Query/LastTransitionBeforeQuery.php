<?php

namespace Marble\JiraKpi\Domain\Repository\Query;

use Carbon\CarbonImmutable;

readonly class LastTransitionBeforeQuery
{
    public function __construct(
        public CarbonImmutable $before,
    ) {
    }
}
