<?php

namespace Marble\JiraKpi\Domain\Model\Issue;

use Carbon\CarbonImmutable;
use Marble\Entity\Entity;
use Marble\Entity\Identifier;
use Marble\Entity\SimpleId;
use Marble\JiraKpi\Domain\Model\Unit\StoryPoint;

class Issue implements Entity
{
    public function __construct(
        private readonly SimpleId $key,
        private IssueType         $type,
        private CarbonImmutable   $created,
        private string            $summary,
        private ?StoryPoint       $estimate,
        private IssueStatus       $status,
    ) {
    }

    public function getId(): Identifier
    {
        return $this->key;
    }

    public function getKey(): string
    {
        return (string) $this->key;
    }

    public function getType(): IssueType
    {
        return $this->type;
    }

    public function setType(IssueType $type): void
    {
        $this->type = $type;
    }

    public function getCreated(): CarbonImmutable
    {
        return $this->created;
    }

    public function setCreated(CarbonImmutable $created): void
    {
        $this->created = $created;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    public function getEstimate(): ?StoryPoint
    {
        return $this->estimate;
    }

    public function setEstimate(?StoryPoint $estimate): void
    {
        $this->estimate = $estimate;
    }

    public function getStatus(): IssueStatus
    {
        return $this->status;
    }

    public function setStatus(IssueStatus $status): void
    {
        $this->status = $status;
    }
}
