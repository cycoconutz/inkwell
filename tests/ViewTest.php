<?php

declare(strict_types=1);

namespace Tests;

use App\Core\View;

final class ViewTest extends TestCaseBase
{
    public function testRendersViewWithLayout(): void
    {
        $dir = sys_get_temp_dir() . '/inkwell_views_' . uniqid();
        mkdir($dir);
        mkdir($dir . '/layouts');
        file_put_contents($dir . '/layouts/app.php', 'L:<?= $content ?>');
        file_put_contents($dir . '/hello.php', 'H:<?= e($name) ?>');

        $ref = new \ReflectionClass(View::class);
        $p = $ref->getProperty('path');
        $p->setAccessible(true);

        $prev = $p->getValue(null);
        $p->setValue(null, static function (string $template) use ($dir) {
            return $dir . '/' . str_replace('.', '/', $template) . '.php';
        });

        ob_start();
        View::render('hello', ['name' => 'World']);
        $out = ob_get_clean();

        $p->setValue(null, $prev);
        array_map('unlink', glob($dir . '/*/*.php'));
        array_map('rmdir', array_filter(glob($dir . '/*')));
        rmdir($dir);

        $this->assertSame('L:H:World', $out);
    }
}
