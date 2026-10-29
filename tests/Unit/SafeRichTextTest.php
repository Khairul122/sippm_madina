<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Support\SafeRichText;
use PHPUnit\Framework\TestCase;

class SafeRichTextTest extends TestCase
{
    public function test_quill_formatting_survives_but_scripts_and_event_handlers_do_not(): void
    {
        $html = '<p onclick="alert(1)">Halo <strong>Madina</strong><img src=x onerror="alert(2)"></p>'
            .'<script>alert(3)</script><ul><li>Daftar</li></ul>';

        $this->assertSame('<p>Halo <strong>Madina</strong></p><ul><li>Daftar</li></ul>', SafeRichText::render($html));
    }
}
