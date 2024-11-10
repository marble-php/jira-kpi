<?php

namespace Marble\JiraKpi\Domain\Model\Result;

use Carbon\CarbonImmutable;
use Marble\JiraKpi\Domain\Model\Unit\Second;
use function Marble\JiraKpi\Domain\array_avg;
use function Marble\JiraKpi\Domain\div;

readonly class MonthlyBugLeadTime
{
    public const int WEEKDAY_SECONDS_IN_WEEK = 5 * 24 * 60 * 60;

    /**
     * @param array<string, Second> $leadTimes // [ key => weekday seconds between bug reported and bug fixed ]
     * @param array<string, Second> $latencies // [ key => weekday seconds between causing-issue done and bug reported ]
     */
    public function __construct(
        public CarbonImmutable $month,
        public int             $fixed,
        public array           $leadTimes,
        public array           $latencies,
    ) {
    }

    public function getAvgLeadTime(): Second
    {
        return new Second(array_avg($this->leadTimes));
    }

    public function getAvgLeadTimeMaxAge(int $weeks): Second
    {
        $leadTimes = array_filter($this->leadTimes, fn(Second $leadTime) => $leadTime->value < $weeks * self::WEEKDAY_SECONDS_IN_WEEK);

        return new Second(array_avg($leadTimes));
    }

    public function getSlowest(int $num): array
    {
        return array_slice(Second::desc($this->leadTimes), 0, $num, true);
    }

    public function getAvgLatency(): Second
    {
        return new Second(array_avg($this->latencies));
    }

    public function getFractionReportedWithin(int $weeks): float
    {
        $filtered = array_filter($this->latencies, fn(Second $latency) => $latency->value < $weeks * self::WEEKDAY_SECONDS_IN_WEEK);

        return div(count($filtered), $this->fixed);
    }

    public function getHottest(int $num): array
    {
        return array_slice(Second::asc($this->latencies), 0, $num, true);
    }
}
