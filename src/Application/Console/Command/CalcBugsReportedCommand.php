<?php

namespace Marble\JiraKpi\Application\Console\Command;

use Marble\JiraKpi\Domain\Model\Result\MonthlyBugCreation;
use Marble\JiraKpi\Domain\Service\KpiCalculator\BugsAnalyzer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Marble\JiraKpi\Domain\div;

#[AsCommand(name: 'app:bug-reports')]
class CalcBugsReportedCommand extends AbstractKpiCommand
{
    public function __construct(
        private readonly BugsAnalyzer $bugsAnalyzer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $analyses = $this->bugsAnalyzer->calculateCreation($this->getNumWholeMonths());

        $this->renderTable($output, ...$analyses);

        return Command::SUCCESS;
    }

    private function renderTable(OutputInterface $output, MonthlyBugCreation ...$analyses): void
    {
        $table   = new Table($output);
        $pastAvg = $this->calcHistoricalAverages(...array_slice($analyses, 0, -2));

        $table->setHeaders(['Month', 'Bugs created', 'Bugs estimated', 'Story points', 'Avg estimate']);

        foreach ($analyses as $index => $analysis) {
            $table->addRow([
                $this->ongoing($analysis->month),
                $analysis->created,
                $analysis->estimated . $this->suffix($analysis->getEstimatedFraction(), true),
                $analysis->storyPoints->value,
                round($analysis->getAvgStoryPointEstimate(), 1),
            ]);

            if ($index === count($analyses) - 3) {
                $this->addHistoricalAveragesRow($table, $pastAvg);
            }
        }

        $table->render();
    }

    private function calcHistoricalAverages(MonthlyBugCreation ...$analyses): array
    {
        $result = [
            'created'      => 0,
            'estimated'    => 0,
            'storyPoints'  => 0,
            'avg-estimate' => 0,
        ];

        foreach ($analyses as $analysis) {
            $result['created']      += $analysis->created;
            $result['estimated']    += $analysis->estimated;
            $result['storyPoints']  += $analysis->storyPoints->value;
            $result['avg-estimate'] += $analysis->getAvgStoryPointEstimate();
        }

        $result['avg-estimate'] = round(div($result['avg-estimate'], count($analyses)), 1);
        $result['storyPoints']  = round(div($result['storyPoints'], count($analyses)), 1);
        $result['estimated']    = round(div($result['estimated'], count($analyses)), 1) . $this->suffix(div($result['estimated'], $result['created']), true);
        $result['created']      = round(div($result['created'], count($analyses)), 1);

        return $result;
    }
}
