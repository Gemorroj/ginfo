<?php

namespace Ginfo\Parser\WebServer;

use Ginfo\Parser\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Angie implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @param string|null $statusPage uri for json status page http://localhost/status/ for example. see https://angie.software/angie/docs/configuration/modules/http/http_stub_status/
     *
     * @return array{angie_version: string, nginx_version: string, build_date: \DateTimeImmutable|null, crypto: string, tls_sni: bool, args: string, status: array|null}|null
     */
    public static function work(?string $statusPage = null): ?array
    {
        $process = new Process(['angie', '-V'], null, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $res = [
            'angie_version' => '',
            'nginx_version' => '',
            'build_date' => null,
            'crypto' => '',
            'tls_sni' => false,
            'args' => '',
            'status' => null,
        ];
        $lines = \explode("\n", \trim($process->getErrorOutput())); // exactly use STDERR. see https://trac.nginx.org/nginx/ticket/592
        foreach ($lines as $line) {
            $line = \trim($line);

            if (\str_starts_with($line, 'Angie version: Angie/')) {
                $res['angie_version'] = \str_replace('Angie version: Angie/', '', $line);
            }
            if (\str_starts_with($line, 'nginx version: nginx/')) {
                $res['nginx_version'] = \str_replace('nginx version: nginx/', '', $line);
            }
            if (\str_starts_with($line, 'built on ')) {
                $res['build_date'] = new \DateTimeImmutable(\str_replace('built on ', '', $line));
            }
            if (\str_starts_with($line, 'built with ')) {
                $res['crypto'] = \str_replace('built with ', '', $line);
            }
            if (\str_contains($line, 'TLS SNI support enabled')) {
                $res['tls_sni'] = true;
            }
            if (\str_contains($line, 'configure arguments: ')) {
                $res['args'] = \str_replace('configure arguments: ', '', $line);
            }
        }

        if ($statusPage) {
            $statusPageContent = @\file_get_contents($statusPage);
            if (false !== $statusPageContent) {
                try {
                    $res['status'] = \json_decode($statusPageContent, true, 512, \JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    // ignore
                }
            }
        }

        return $res;
    }
}
