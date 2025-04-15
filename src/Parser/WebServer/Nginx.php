<?php

namespace Ginfo\Parser\WebServer;

use Ginfo\Parser\ParserInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class Nginx implements ParserInterface
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @param string|null $statusPage uri for json status page http://localhost/status/ for example. see https://nginx.org/en/docs/http/ngx_http_api_module.html
     *
     * @return array{nginx_version: string, crypto: string, tls_sni: bool, args: string, status: array|null}|null
     */
    public static function work(?string $statusPage = null): ?array
    {
        $process = new Process(['nginx', '-V'], null, ['LANG' => 'C']);
        try {
            $process->mustRun();
        } catch (ProcessFailedException|ProcessStartFailedException $e) {
            return null;
        }

        $res = [
            'nginx_version' => '',
            'crypto' => '',
            'tls_sni' => false,
            'args' => '',
            'status' => null,
        ];
        $lines = \explode("\n", \trim($process->getOutput()));
        foreach ($lines as $line) {
            $line = \trim($line);

            if (\str_starts_with($line, 'nginx version: nginx/')) {
                $res['nginx_version'] = \str_replace('nginx version: nginx/', '', $line);
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
