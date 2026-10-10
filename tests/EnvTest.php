<?php

declare(strict_types=1);

namespace Tests;

use App\Support\Env;

final class EnvTest extends TestCaseBase
{
    public function testLoadsFromFile(): void
    {
        $path = sys_get_temp_dir() . '/inkwell_env_' . uniqid() . '.env';
        file_put_contents($path, "FOO=bar\nBAZ=qux\n");
        Env::load($path);
        $this->assertSame('bar', Env::get('FOO'));
        $this->assertSame('qux', Env::get('BAZ'));
        @unlink($path);
    }

    public function testReturnsDefault(): void
    {
        $this->assertSame('missing', Env::get('DOES_NOT_EXIST', 'missing'));
    }
}
