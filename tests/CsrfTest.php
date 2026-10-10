<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Csrf;
use App\Core\Session;

final class CsrfTest extends TestCaseBase
{
    public function testTokenIsStableWithinSession(): void
    {
        Session::start('test');
        $t1 = Csrf::token();
        $t2 = Csrf::token();
        $this->assertSame($t1, $t2);
    }

    public function testCheckRejectsWrongToken(): void
    {
        Session::start('test');
        Csrf::token();
        $this->assertFalse(Csrf::check('nope'));
    }

    public function testCheckAcceptsToken(): void
    {
        Session::start('test');
        $t = Csrf::token();
        $this->assertTrue(Csrf::check($t));
    }
}
