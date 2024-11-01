<?php

namespace Marble\JiraKpi\Infrastructure\Database\Mapper;

use Doctrine\DBAL\Query\QueryBuilder;
use Marble\JiraKpi\Domain\Model\Issue\Issue;
use Marble\JiraKpi\Domain\Model\Issue\IssueStatus;
use Marble\JiraKpi\Domain\Model\Issue\IssueType;
use Marble\JiraKpi\Domain\Repository\Query\BugsReportedBetweenQuery;
use Marble\JiraKpi\Domain\Repository\Query\TransitionedToStatusBetweenQuery;

class IssueMapper extends BaseEntityMapper
{
    public static function getEntityClassName(): string
    {
        return Issue::class;
    }

    protected function idField(): string
    {
        return 'key';
    }

    protected function whereDoneBetween(QueryBuilder $sqlBuilder, TransitionedToStatusBetweenQuery $query): void
    {
        $sqlBuilder->join('i', 'transition', 't', 'i.`key` = t.issue');
        $sqlBuilder->where('t.`to` = ' . $this->toSqlParam($sqlBuilder, IssueStatus::DONE));
        $sqlBuilder->andWhere('t.transitioned >= ' . $this->toSqlParam($sqlBuilder, $query->after));
        $sqlBuilder->andWhere('t.transitioned < ' . $this->toSqlParam($sqlBuilder, $query->before));
    }

    protected function whereBugsReportedBetween(QueryBuilder $sqlBuilder, BugsReportedBetweenQuery $query): void
    {
        $sqlBuilder->where('i.type = ' . $this->toSqlParam($sqlBuilder, IssueType::BUG));
        $sqlBuilder->andWhere('i.created >= ' . $this->toSqlParam($sqlBuilder, $query->after));
        $sqlBuilder->andWhere('i.created < ' . $this->toSqlParam($sqlBuilder, $query->before));
    }
}
