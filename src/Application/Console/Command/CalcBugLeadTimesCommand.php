<?php

namespace Marble\JiraKpi\Application\Console\Command;

use Marble\JiraKpi\Domain\Model\Result\MonthlyBugLeadTime;
use Marble\JiraKpi\Domain\Model\Unit\Second;
use Marble\JiraKpi\Domain\Service\KpiCalculator\BugsAnalyzer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Marble\JiraKpi\Domain\div;

#[AsCommand(name: 'app:bug-lead')]
class CalcBugLeadTimesCommand extends AbstractKpiCommand
{
    public function __construct(
        private readonly BugsAnalyzer $bugsAnalyzer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $analyses = $this->bugsAnalyzer->calculateLeadTime($this->getNumWholeMonths());

        $this->renderTable($output, ...$analyses);

        return Command::SUCCESS;
    }

    private function renderTable(OutputInterface $output, MonthlyBugLeadTime ...$analyses): void
    {
        $table   = new Table($output);
        $pastAvg = $this->calcHistoricalAverages(...array_slice($analyses, 0, -2));

        $table->setHeaders(['Month', 'Bugs fixed', 'Avg bug lead time', 'Max 6 months old', 'Max 2 months old', 'Slowest bug', '2nd slowest', '3rd slowest']);

        foreach ($analyses as $index => $analysis) {
            $slowest = array_map(fn(string $key, Second $leadTime): string => sprintf('%s%s', $key, $this->suffix($leadTime->toDay()->value)),
                array_keys($analysis->slowest), $analysis->slowest);

            $table->addRow([
                $this->ongoing($analysis->month),
                $analysis->fixed,
                round($analysis->avgLeadTime->toDay()->value, 1),
                round($analysis->avgLeadTimeMax2Months->toDay()->value, 1),
                round($analysis->avgLeadTimeMax2Months->toDay()->value, 1),
                ...array_slice($slowest, 0, 3),
            ]);

            if ($index === count($analyses) - 3) {
                $this->addHistoricalAveragesRow($table, $pastAvg, 3);
            }
        }

        $table->render();
    }

    private function calcHistoricalAverages(MonthlyBugLeadTime ...$analyses): array
    {
        $result = [
            'fixed'         => 0,
            'avg-lead-time' => 0,
            'max-6-months'  => 0,
            'max-2-months'  => 0,
        ];

        foreach ($analyses as $analysis) {
            $result['fixed']         += $analysis->fixed;
            $result['avg-lead-time'] += $analysis->fixed * $analysis->avgLeadTime->toDay()->value;
            $result['max-6-months']  += $analysis->fixed * $analysis->avgLeadTimeMax6Months->toDay()->value;
            $result['max-2-months']  += $analysis->fixed * $analysis->avgLeadTimeMax2Months->toDay()->value;
        }

        $result['avg-lead-time'] = round(div($result['avg-lead-time'], $result['fixed']), 1);
        $result['max-6-months']  = round(div($result['max-6-months'], $result['fixed']), 1);
        $result['max-2-months']  = round(div($result['max-2-months'], $result['fixed']), 1);
        $result['fixed']         = round(div($result['fixed'], count($analyses)), 1);

        return $result;
    }
}
