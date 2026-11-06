<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        unset($_SESSION['flash_messages']);
    }

    public function testFormatRupiahWithZero(): void
    {
        $this->assertSame('Rp 0', format_rupiah(0));
    }

    public function testFormatRupiahWithStandardPrice(): void
    {
        $this->assertSame('Rp 25.000', format_rupiah(25000));
        $this->assertSame('Rp 9.500', format_rupiah(9500));
    }

    public function testFormatRupiahWithLargeAmount(): void
    {
        $this->assertSame('Rp 1.500.000', format_rupiah(1500000));
    }

    public function testSanitizeStripsHtmlAndEntities(): void
    {
        $dirty = '<script>alert("xss")</script><b>Hello</b>';
        $clean = sanitize($dirty);
        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertSame('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;&lt;b&gt;Hello&lt;/b&gt;', $clean);
    }

    public function testSanitizeTrimsWhitespace(): void
    {
        $this->assertSame('clean text', sanitize("  clean text   \n"));
    }

    public function testFlashMessageLifecycle(): void
    {
        flash('success', 'Operasi berhasil disimpan!');
        $this->assertNotEmpty($_SESSION['flash_messages']);

        $rendered = render_flash();
        $this->assertStringContainsString('alert-success', $rendered);
        $this->assertStringContainsString('Operasi berhasil disimpan!', $rendered);

        // Flash message must be cleared once rendered
        $this->assertEmpty($_SESSION['flash_messages'] ?? []);
        $this->assertSame('', render_flash());
    }

    public function testRedirectThrowsExceptionInTestingMode(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Redirected to: /auth/login');

        redirect('auth/login');
    }
}
