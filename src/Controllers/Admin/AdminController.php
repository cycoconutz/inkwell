<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;

/**
 * Every admin controller is constructed behind this guard: no session, no
 * admin page. (The router instantiates the controller before calling the
 * action, so the check runs first.)
 */
abstract class AdminController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirect(url('login'));
        }
    }
}
