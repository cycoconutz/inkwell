<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Request;

final class RequestTest extends TestCaseBase
{
    public function testNormalizesRootPath(): void
    {
        $req = $this->newRequest('GET', '/');
        $this->assertSame('/', $req->path());
    }

    public function testNormalizesSubpath(): void
    {
        $req = $this->newRequest('GET', '/blog');
        $this->assertSame('/blog', $req->path());
    }

    public function testHonorsMethodOverride(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/admin/posts/1';
        $_POST = ['_method' => 'delete'];
        $req = Request::fromGlobals();
        $this->assertSame('DELETE', $req->method());
    }

    public function testReadsBody(): void
    {
        $req = $this->newRequest('POST', '/login', ['email' => 'admin@inkwell.test']);
        $this->assertSame('admin@inkwell.test', $req->input('email'));
    }
}
