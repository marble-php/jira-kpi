<?php

namespace Marble\JiraKpi\Domain\Model\Unit;

readonly abstract class Unit
{
    public function __construct(
        public float $value,
    ) {
    }

    public static function asc(array $values): array
    {
        uasort($values, fn(Unit $a, Unit $b) => $a->value <=> $b->value);

        return $values;
    }

    public static function desc(array $values): array
    {
        uasort($values, fn(Unit $a, Unit $b) => $b->value <=> $a->value);

        return $values;
    }
}
