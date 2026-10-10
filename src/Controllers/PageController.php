<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

final class PageController extends Controller
{
    public function about(Request $request): void
    {
        $this->view('about', [], 'layouts/app');
    }
}
