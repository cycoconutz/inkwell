<?php

declare(strict_types=1);

namespace App\Core;

/**
 * A deliberately small Markdown subset: headings, paragraphs, unordered and
 * ordered lists, blockquotes, fenced code, horizontal rules, and inline
 * bold/italic/code/links.
 *
 * Safety model: the whole input is HTML-escaped first, then a fixed set of tags
 * is introduced. Nothing from the source can become a tag, and links are
 * restricted to http(s), mailto, and relative targets.
 */
final class Markdown
{
    public static function toHtml(string $markdown): string
    {
        $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);
        $lines = explode("\n", $markdown);
        $count = count($lines);
        $html = '';
        $i = 0;

        while ($i < $count) {
            $line = rtrim($lines[$i]);

            if (str_starts_with(trim($line), '```')) {
                $i++;
                $code = [];
                while ($i < $count && !str_starts_with(trim($lines[$i]), '```')) {
                    $code[] = $lines[$i];
                    $i++;
                }
                $i++; // closing fence
                $html .= '<pre><code>' . e(implode("\n", $code)) . '</code></pre>';
                continue;
            }

            if (trim($line) === '') {
                $i++;
                continue;
            }

            if (preg_match('/^(#{1,6})\s+(.*)$/', $line, $m)) {
                $level = strlen($m[1]);
                $html .= "<h{$level}>" . self::inline($m[2]) . "</h{$level}>";
                $i++;
                continue;
            }

            if (preg_match('/^(-{3,}|\*{3,}|_{3,})$/', trim($line))) {
                $html .= '<hr>';
                $i++;
                continue;
            }

            if (str_starts_with(ltrim($line), '>')) {
                $quote = [];
                while ($i < $count && str_starts_with(ltrim($lines[$i]), '>')) {
                    $quote[] = (string) preg_replace('/^\s*>\s?/', '', $lines[$i]);
                    $i++;
                }
                $html .= '<blockquote>' . self::inline(implode(' ', $quote)) . '</blockquote>';
                continue;
            }

            if (preg_match('/^\s*[-*+]\s+(.*)$/', $line, $m)) {
                $html .= '<ul>';
                while ($i < $count && preg_match('/^\s*[-*+]\s+(.*)$/', $lines[$i], $item)) {
                    $html .= '<li>' . self::inline($item[1]) . '</li>';
                    $i++;
                }
                $html .= '</ul>';
                continue;
            }

            if (preg_match('/^\s*\d+[.)]\s+(.*)$/', $line, $m)) {
                $html .= '<ol>';
                while ($i < $count && preg_match('/^\s*\d+[.)]\s+(.*)$/', $lines[$i], $item)) {
                    $html .= '<li>' . self::inline($item[1]) . '</li>';
                    $i++;
                }
                $html .= '</ol>';
                continue;
            }

            $paragraph = [$line];
            $i++;
            while ($i < $count && self::continuesParagraph($lines[$i])) {
                $paragraph[] = rtrim($lines[$i]);
                $i++;
            }
            $html .= '<p>' . self::inline(implode("\n", $paragraph)) . '</p>';
        }

        return $html;
    }

    private static function continuesParagraph(string $line): bool
    {
        $trimmed = trim($line);

        if ($trimmed === '') {
            return false;
        }

        foreach (
            [
                '/^#{1,6}\s+/',
                '/^\s*[-*+]\s+/',
                '/^\s*\d+[.)]\s+/',
                '/^(-{3,}|\*{3,}|_{3,})$/',
            ] as $pattern
        ) {
            if (preg_match($pattern, $line)) {
                return false;
            }
        }

        return !str_starts_with(ltrim($line), '>') && !str_starts_with($trimmed, '```');
    }

    private static function inline(string $text): string
    {
        // Escape first. Everything below only introduces tags we control.
        $text = e($text);

        $text = (string) preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
        $text = (string) preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text);
        $text = (string) preg_replace('/(?<![\*\w])\*([^*\n]+)\*(?!\*)/', '<em>$1</em>', $text);

        return (string) preg_replace_callback(
            '/\[([^\]]+)\]\(([^)\s]+)\)/',
            static function (array $m): string {
                $url = html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if (!preg_match('#^(https?://|mailto:|/|\#)#i', $url)) {
                    return $m[1];
                }

                $external = (bool) preg_match('#^https?://#i', $url);
                $attrs = $external ? ' rel="noopener noreferrer" target="_blank"' : '';

                return '<a href="' . e($url) . '"' . $attrs . '>' . $m[1] . '</a>';
            },
            $text,
        );
    }
}
