<?php

namespace Ginfo\Info\Database;

use Ginfo\Info\InfoInterface;

final readonly class Elasticsearch implements InfoInterface
{
    public function __construct(
        private ElasticsearchStats $stats,
    ) {
    }

    public function getStats(): ElasticsearchStats
    {
        return $this->stats;
    }
}
