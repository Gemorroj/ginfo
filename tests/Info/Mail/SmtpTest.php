<?php

namespace Ginfo\Tests\Info\Mail;

use Ginfo\Info\Mail\Smtp;
use PHPUnit\Framework\TestCase;

final class SmtpTest extends TestCase
{
    public function testAvailable(): void
    {
        $smtp = new Smtp(
            'smtp.example.com',
            587,
            Smtp::ENCRYPTION_STARTTLS,
            'smtp.example.com ESMTP',
            true,
            ['SIZE' => '35882577', 'STARTTLS' => null],
            true,
            12.5,
        );

        self::assertSame('smtp.example.com', $smtp->getHost());
        self::assertSame(587, $smtp->getPort());
        self::assertSame(Smtp::ENCRYPTION_STARTTLS, $smtp->getEncryption());
        self::assertSame('smtp.example.com ESMTP', $smtp->getBanner());
        self::assertTrue($smtp->isTls());
        self::assertSame(['SIZE' => '35882577', 'STARTTLS' => null], $smtp->getCapabilities());
        self::assertTrue($smtp->hasCapability('size'));
        self::assertTrue($smtp->hasCapability('STARTTLS'));
        self::assertFalse($smtp->hasCapability('AUTH'));
        self::assertTrue($smtp->isAuthenticated());
        self::assertSame(12.5, $smtp->getTime());
        self::assertNull($smtp->getError());
        self::assertTrue($smtp->isAvailable());
    }

    public function testUnavailable(): void
    {
        $smtp = new Smtp('smtp.example.com', 25, error: 'Connection refused');

        self::assertFalse($smtp->isAvailable());
        self::assertSame('Connection refused', $smtp->getError());
        self::assertNull($smtp->getBanner());
        self::assertNull($smtp->isAuthenticated());
        self::assertFalse($smtp->isTls());
        self::assertSame([], $smtp->getCapabilities());
        self::assertSame(Smtp::ENCRYPTION_NONE, $smtp->getEncryption());
    }
}
