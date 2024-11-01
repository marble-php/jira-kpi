<?php

namespace Marble\JiraKpi\Domain\Service\KpiCalculator;

use Carbon\CarbonImmutable;
use Marble\EntityManager\EntityManager;
use Marble\JiraKpi\Domain\Model\Issue\Issue;
use Marble\JiraKpi\Domain\Model\Issue\IssueStatus;
use Marble\JiraKpi\Domain\Model\Issue\IssueTransition;
use Marble\JiraKpi\Domain\Model\Issue\IssueType;
use Marble\JiraKpi\Domain\Model\Issue\Timeslot;
use Marble\JiraKpi\Domain\Model\Result\MonthlyBugCreation;
use Marble\JiraKpi\Domain\Model\Result\MonthlyBugFixingTime;
use Marble\JiraKpi\Domain\Model\Result\MonthlyBugLeadTime;
use Marble\JiraKpi\Domain\Model\Unit\Second;
use Marble\JiraKpi\Domain\Model\Unit\StoryPoint;
use Marble\JiraKpi\Domain\Repository\Query\BugsReportedBetweenQuery;
use Marble\JiraKpi\Domain\Repository\Query\LatestTransitionQuery;
use Marble\JiraKpi\Domain\Repository\Query\TransitionedToStatusBetweenQuery;
use Marble\JiraKpi\Domain\Service\TimeslotCalculator;
use function Marble\JiraKpi\Domain\array_avg;

class BugsAnalyzer extends AbstractKpiCalculator
{
    public function __construct(
        private readonly EntityManager      $entityManager,
        private readonly TimeslotCalculator $timeslotCalculator,
    ) {
    }

    /**
     * @param int $numWholeMonths
     * @return list<MonthlyBugCreation>
     */
    public function calculateCreation(int $numWholeMonths): array
    {
        return $this->perMonth($numWholeMonths, function (CarbonImmutable $month): MonthlyBugCreation {
            $issues      = $this->entityManager->getRepository(Issue::class)->fetchMany(new BugsReportedBetweenQuery($month, $month->addMonth()));
            $created     = count($issues);
            $estimated   = count(array_filter($issues, fn(Issue $issue): bool => $issue->getEstimate() !== null));
            $storyPoints = array_sum(array_map(fn(Issue $issue): int => $issue->getEstimate()->value ?? 0, $issues));

            return new MonthlyBugCreation($month, $created, $estimated, new StoryPoint($storyPoints));
        });
    }

    /**
     * @param int $numWholeMonths
     * @return list<MonthlyBugLeadTime>
     */
    public function calculateLeadTime(int $numWholeMonths): array
    {
        return $this->perMonth($numWholeMonths, function (CarbonImmutable $month): MonthlyBugLeadTime {
            $query  = new TransitionedToStatusBetweenQuery(IssueStatus::DONE, $month, $month->addMonth());
            $issues = $this->entityManager->getRepository(Issue::class)->fetchMany($query);
            /** @var list<Issue> $issues */
            $issues     = array_filter($issues, fn(Issue $issue): bool => $issue->getType() === IssueType::BUG);
            $fixed      = count($issues);
            $leadTimesX = $leadTimes6 = $leadTimes2 = [];

            foreach ($issues as $issue) {
                $transition = $this->entityManager->getRepository(IssueTransition::class)->fetchOne(new LatestTransitionQuery($issue, IssueStatus::DONE));

                if ($transition instanceof IssueTransition) {
                    $leadTimesX[$issue->getKey()] = $diff = $issue->getCreated()->diffInWeekdaySeconds($transition->getTransitioned());

                    if ($issue->getCreated() > $transition->getTransitioned()->subMonths(6)) {
                        $leadTimes6[$issue->getKey()] = $diff;

                        if ($issue->getCreated() > $transition->getTransitioned()->subMonths(2)) {
                            $leadTimes2[$issue->getKey()] = $diff;
                        }
                    }
                }
            }

            arsort($leadTimesX);

            $slowest = array_slice($leadTimesX, 0, 3, true);
            $slowest = array_map(fn(int $value): Second => new Second($value), $slowest);

            return new MonthlyBugLeadTime(
                $month,
                $fixed,
                new Second(array_avg($leadTimesX)),
                new Second(array_avg($leadTimes6)),
                new Second(array_avg($leadTimes2)),
                $slowest,
            );
        });
    }

    public function calculateTimeFixingBugs(int $monthsBeforeLast): array
    {
        return $this->perMonth($monthsBeforeLast, function (CarbonImmutable $month): MonthlyBugFixingTime {
            $end         = $month->addMonth();
            $timeslots   = $this->timeslotCalculator->getTimeslotsOverlappingWith($month, $end);
            $activeTs    = array_filter($timeslots, fn(Timeslot $timeslot): bool => $timeslot->status->isActive() && $timeslot->issue->getType()->hasUsefulActiveTime());
            $onAny       = array_sum(array_map(fn(Timeslot $timeslot): int => $timeslot->getDurationBetween($month, $end)->value, $activeTs));
            $activeBugTs = array_filter($activeTs, fn(Timeslot $timeslot): bool => $timeslot->issue->getType() === IssueType::BUG);
            $onBugs      = array_sum(array_map(fn(Timeslot $timeslot): int => $timeslot->getDurationBetween($month, $end)->value, $activeBugTs));

            usort($activeBugTs, fn(Timeslot $a, Timeslot $b): int => $b->getDuration()->value <=> $a->getDuration()->value);

            $slowest = array_slice($activeBugTs, 0, 3);

            return new MonthlyBugFixingTime(
                $month,
                new Second($onAny),
                new Second($onBugs),
                $slowest,
            );
        });
    }
}
