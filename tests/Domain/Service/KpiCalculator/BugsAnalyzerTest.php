<?php

namespace Domain\Service\KpiCalculator;

use Carbon\CarbonImmutable;
use Marble\EntityManager\EntityManager;
use Marble\EntityManager\Repository\Repository;
use Marble\JiraKpi\Domain\Model\Issue\Issue;
use Marble\JiraKpi\Domain\Model\Result\MonthlyBugCreation;
use Marble\JiraKpi\Domain\Model\Unit\StoryPoint;
use Marble\JiraKpi\Domain\Repository\Query\BugsReportedBetweenQuery;
use Marble\JiraKpi\Domain\Service\KpiCalculator\BugsAnalyzer;
use Marble\JiraKpi\Domain\Service\TimeslotCalculator;
use Marble\JiraKpi\Tests\AbstractTestCase;
use Mockery;

class BugsAnalyzerTest extends AbstractTestCase
{
    public function testCalculateCreation(): void
    {
        CarbonImmutable::setTestNow('2024-11-01 12:00:00');

        $entityManager      = Mockery::mock(EntityManager::class);
        $timeslotCalculator = Mockery::mock(TimeslotCalculator::class);
        $issueRepo          = Mockery::mock(Repository::class);

        $issueA = $this->mockIssue('A', '2024-10-08');
        $issueA->expects('getEstimate')->atLeast()->once()->andReturn(new StoryPoint(8));

        $entityManager->expects()->getRepository(Issue::class)->atLeast()->once()->andReturn($issueRepo);
        $issueRepo->expects()->fetchMany(Mockery::type(BugsReportedBetweenQuery::class))->atLeast()->once()->andReturn([
            $issueA,
        ]);

        $bugsAnalyzer = new BugsAnalyzer($entityManager, $timeslotCalculator);
        $analyses     = $bugsAnalyzer->calculateCreation(1);

        $this->assertIsArray($analyses);
        $this->assertCount(2, $analyses);
        $this->assertTrue(array_is_list($analyses));
        $this->assertInstanceOf(MonthlyBugCreation::class, $analyses[0]);
        $this->assertInstanceOf(MonthlyBugCreation::class, $analyses[1]);
    }
}
