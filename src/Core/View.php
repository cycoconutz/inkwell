<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Renders a view file into a layout. Shared data is extracted into the view's
 * local scope; the rendered view is available to the layout as $content.
 */
final class View
{
    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $template, array $data = [], string $layout = 'layouts/app'): void
    {
        $content = self::capture($template, $data);

        if ($layout === '') {
            echo $content;
            return;
        }

        echo self::capture($layout, array_merge($data, ['content' => $content]));
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function capture(string $template, array $data): string
    {
        $file = self::path($template);

        if (!is_file($file)) {
            throw new RuntimeException("View not found: {$template}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $file;
        return (string) ob_get_clean();
    }

    private static function path(string $template): string
    {
        return base_path('views/' . str_replace('.', '/', $template) . '.php');
    }
}
