<?php

namespace Ginfo\Info\Database;

final readonly class SqlitePragma
{
    /**
     * @param string[]            $collationList
     * @param string[]            $compileOptions
     * @param SqlitePragmaTable[] $tableList
     */
    public function __construct(
        private int $autoVacuum,
        private int $automaticIndex,
        private int $busyTimeout,
        private int $cacheSize,
        private string $encoding,
        private int $ignoreCheckConstraints,
        private bool $incrementalVacuum,
        private string $journalMode,
        private int $journalSizeLimit,
        private string $lockingMode,
        private int $pageCount,
        private int $pageSize,
        private string $quickCheck,
        private int $readUncommitted,
        private int $secureDelete,
        private int $synchronous,
        private int $threads,
        private int $trustedSchema,
        private int $walAutocheckpoint,
        private array $collationList,
        private array $compileOptions,
        private array $tableList,
    ) {
    }

    public function getAutoVacuum(): int
    {
        return $this->autoVacuum;
    }

    public function getAutomaticIndex(): int
    {
        return $this->automaticIndex;
    }

    public function getBusyTimeout(): int
    {
        return $this->busyTimeout;
    }

    public function getCacheSize(): int
    {
        return $this->cacheSize;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getIgnoreCheckConstraints(): int
    {
        return $this->ignoreCheckConstraints;
    }

    public function isIncrementalVacuum(): bool
    {
        return $this->incrementalVacuum;
    }

    public function getJournalMode(): string
    {
        return $this->journalMode;
    }

    public function getJournalSizeLimit(): int
    {
        return $this->journalSizeLimit;
    }

    public function getLockingMode(): string
    {
        return $this->lockingMode;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getQuickCheck(): string
    {
        return $this->quickCheck;
    }

    public function getReadUncommitted(): int
    {
        return $this->readUncommitted;
    }

    public function getSecureDelete(): int
    {
        return $this->secureDelete;
    }

    public function getSynchronous(): int
    {
        return $this->synchronous;
    }

    public function getThreads(): int
    {
        return $this->threads;
    }

    public function getTrustedSchema(): int
    {
        return $this->trustedSchema;
    }

    public function getWalAutocheckpoint(): int
    {
        return $this->walAutocheckpoint;
    }

    /**
     * @return string[]
     */
    public function getCollationList(): array
    {
        return $this->collationList;
    }

    /**
     * @return string[]
     */
    public function getCompileOptions(): array
    {
        return $this->compileOptions;
    }

    /**
     * @return SqlitePragmaTable[]
     */
    public function getTableList(): array
    {
        return $this->tableList;
    }
}
