<?php

namespace Marble\JiraKpi\Domain\Model\Unit;

readonly abstract class Unit
{
    public function __construct(
        public float $value,
    ) {
    }

    public function __invoke(): float
    {
        return $this->value;
    }
}
