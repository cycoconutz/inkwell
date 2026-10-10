<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Support\Env;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/Support/autoload.php';

abstract class TestCaseBase extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_POST = [];
        $_GET = [];
        $_FILES = [];
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/',
            'SERVER_NAME'    => 'localhost',
        ];
    }

    protected function newRequest(string $method, string $uri, array $body = [], array $query = []): Request
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI']    = $uri;
        $_POST = $body;
        $_GET  = $query;
        return Request::fromGlobals();
    }

    protected function withCsrf(array $body = []): array
    {
        Session::start('test');
        $token = Csrf::token();
        return array_merge(['_token' => $token], $body);
    }
}
