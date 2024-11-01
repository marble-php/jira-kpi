<?php

namespace Marble\JiraKpi\Application\Console\Command;

use Marble\JiraKpi\Domain\Model\Issue\IssueType;
use Marble\JiraKpi\Domain\Model\Result\MonthlyVelocity;
use Marble\JiraKpi\Domain\Service\KpiCalculator\VelocityCalculator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Marble\JiraKpi\Domain\div;
use function Symfony\Component\String\u;

#[AsCommand(name: 'app:velocity')]
class CalcVelocityCommand extends AbstractKpiCommand
{
    public function __construct(
        private readonly VelocityCalculator $calculator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $velocities = $this->calculator->calculate($this->getNumWholeMonths());

        $this->renderTable($output, ...$velocities);

        return Command::SUCCESS;
    }

    private function renderTable(OutputInterface $output, MonthlyVelocity ...$velocities): void
    {
        $table       = new Table($output);
        $typeNames   = array_column(IssueType::cases(), 'name');
        $typeHeaders = array_map(fn(string $name): string => u($name)->lower()->title(), $typeNames);
        $pastAvg     = $this->calcHistoricalAverages(...array_slice($velocities, 0, -2));

        $table->setHeaders(['Month', ...$typeHeaders, 'Total']);

        foreach ($velocities as $index => $velocity) {
            $storyPointsPerType = array_fill_keys($typeNames, 0);

            foreach ($velocity->storyPointsPerIssueType as $type => $storyPoints) {
                $storyPointsPerType[$type] = $storyPoints->value . $this->suffix($velocity->getFractionByType(IssueType::{$type}), true);
            }

            $table->addRow([
                $this->ongoing($velocity->month),
                ...$storyPointsPerType,
                $velocity->getTotal(),
            ]);

            if ($index === count($velocities) - 3) {
                $this->addHistoricalAveragesRow($table, $pastAvg);
            }
        }

        $table->render();
    }

    private function calcHistoricalAverages(MonthlyVelocity ...$velocities): array
    {
        $typeNames          = array_column(IssueType::cases(), 'name');
        $storyPointsPerType = array_fill_keys($typeNames, 0);

        foreach ($velocities as $velocity) {
            foreach ($velocity->storyPointsPerIssueType as $type => $storyPoints) {
                $storyPointsPerType[$type] += $storyPoints->value;
            }
        }

        $total = array_sum($storyPointsPerType);

        foreach ($storyPointsPerType as $type => $storyPoints) {
            $storyPointsPerType[$type] = round(div($storyPoints, count($velocities)), 1) . $this->suffix(div($storyPoints, $total), true);
        }

        $storyPointsPerType['*'] = round(div($total, count($velocities)), 1);

        return $storyPointsPerType;
    }
}
