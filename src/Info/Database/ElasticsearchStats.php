<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class ElasticsearchStats implements InfoInterface
{
    public function __construct(
        private string $clusterName,
        private string $clusterUuid,
        private int $timestamp,
        private string $status,
        private ElasticsearchStatsNodes $nodes,
        private ElasticsearchStatsIndices $indices,
    ) {
    }

    public function getClusterName(): string
    {
        return $this->clusterName;
    }

    public function getClusterUuid(): string
    {
        return $this->clusterUuid;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNodes(): ElasticsearchStatsNodes
    {
        return $this->nodes;
    }

    public function getIndices(): ElasticsearchStatsIndices
    {
        return $this->indices;
    }
}
