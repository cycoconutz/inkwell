<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;

final class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 900; // 15 minutes

    public function showLogin(Request $request): void
    {
        if (Auth::check()) {
            $this->redirect(url('admin'));
        }

        $this->view('auth/login', [
            'email' => (string) old('email', ''),
        ], 'layouts/auth');
    }

    public function login(Request $request): void
    {
        $this->verifyCsrf($request);

        if ($this->isLockedOut()) {
            flash('error', 'Too many attempts. Please wait a few minutes and try again.');
            $this->redirect(url('login'));
        }

        $email    = $request->string('email');
        $password = (string) $request->input('password', '');

        if ($email === '' || $password === '') {
            $this->recordAttempt();
            Session::set('_old', ['email' => $email]);
            flash('error', 'Enter your email and password.');
            $this->redirect(url('login'));
        }

        if (Auth::attempt($email, $password)) {
            Session::remove('login_attempts');
            Session::remove('_old');
            $this->redirect(url('admin'));
        }

        $this->recordAttempt();
        Session::set('_old', ['email' => $email]);
        flash('error', 'Those credentials do not match our records.');
        $this->redirect(url('login'));
    }

    public function logout(Request $request): void
    {
        $this->verifyCsrf($request);
        Auth::logout();
        $this->redirect(url('/'));
    }

    private function isLockedOut(): bool
    {
        $state = Session::get('login_attempts');

        if (!is_array($state) || !isset($state['count'], $state['at'])) {
            return false;
        }

        if ((time() - (int) $state['at']) > self::LOCK_SECONDS) {
            Session::remove('login_attempts');
            return false;
        }

        return (int) $state['count'] >= self::MAX_ATTEMPTS;
    }

    private function recordAttempt(): void
    {
        $state = Session::get('login_attempts');

        if (!is_array($state) || !isset($state['at']) || (time() - (int) $state['at']) > self::LOCK_SECONDS) {
            $state = ['count' => 0, 'at' => time()];
        }

        $state['count'] = (int) $state['count'] + 1;
        Session::set('login_attempts', $state);
    }
}
