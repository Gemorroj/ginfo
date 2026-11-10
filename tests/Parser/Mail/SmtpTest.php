<?php

namespace Ginfo\Tests\Parser\Mail;

use Ginfo\Ginfo;
use PHPUnit\Framework\TestCase;

final class SmtpTest extends TestCase
{
    public function testSmtp(): void
    {
        $smtp = (new Ginfo())->getSmtp('127.0.0.1', 1025);
        if (!$smtp->isAvailable()) {
            self::markTestSkipped('SMTP is not found');
        }

        self::assertSame('127.0.0.1', $smtp->getHost());
        self::assertSame(1025, $smtp->getPort());
        self::assertNotEmpty($smtp->getBanner());
        self::assertNotEmpty($smtp->getCapabilities());
        self::assertFalse($smtp->isTls());
        self::assertNull($smtp->isAuthenticated());
        self::assertGreaterThan(0, $smtp->getTime());
    }

    public function testSmtpAuth(): void
    {
        $smtp = (new Ginfo())->getSmtp('127.0.0.1', 1025, username: 'user', password: 'pass');
        if (null === $smtp->getBanner()) {
            self::markTestSkipped('SMTP is not found');
        }

        self::assertTrue($smtp->isAvailable(), (string) $smtp->getError());
        self::assertTrue($smtp->isAuthenticated());
        self::assertTrue($smtp->hasCapability('AUTH'));
    }
}
