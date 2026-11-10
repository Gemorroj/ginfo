<?php

namespace Ginfo\Tests\Fixtures;

use Symfony\Component\Process\Process;

final class SmtpServer
{
    private readonly Process $process;
    private readonly int $port;

    /**
     * @param string[] $arguments
     */
    public function __construct(array $arguments = [])
    {
        $this->process = new Process([\PHP_BINARY, __DIR__.'/smtp-server.php', ...$arguments]);
        $this->process->setTimeout(30.0);
        $this->process->start();

        $started = $this->process->waitUntil(static function (string $type, string $buffer): bool {
            return \str_contains($buffer, 'LISTENING');
        });
        if (!$started) {
            throw new \RuntimeException('The test SMTP server did not start: '.$this->process->getErrorOutput());
        }

        \preg_match('/LISTENING (\d+)/', $this->process->getOutput(), $matches);
        $this->port = (int) ($matches[1] ?? 0);
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function stop(): void
    {
        if ($this->process->isRunning()) {
            $this->process->stop(0);
        }
    }
}
