<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Markdown;

final class MarkdownTest extends TestCaseBase
{
    public function testEscapesHtml(): void
    {
        $this->assertSame('&lt;script&gt;', trim(Markdown::toHtml('<script>')));
    }

    public function testHeadingsAndLinks(): void
    {
        $md = "# Hi\n\n[link](http://example.com)";
        $html = Markdown::toHtml($md);
        $this->assertStringContainsString('<h1>Hi</h1>', $html);
        $this->assertStringContainsString('<a href="http://example.com"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
    }

    public function testStripsUnsafeLinks(): void
    {
        $html = Markdown::toHtml('[x](javascript:alert(1))');
        $this->assertSame('<p>x</p>', trim($html));
    }

    public function testFencedCodeEscaped(): void
    {
        $html = Markdown::toHtml("```\n<?php echo 1; ?>\n```");
        $this->assertStringContainsString('&lt;?php', $html);
    }
}
