<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Models\Category;
use App\Models\Post;

final class DashboardController extends AdminController
{
    public function index(Request $request): void
    {
        $this->view('admin/dashboard', [
            'user'       => Auth::user(),
            'stats'      => Post::stats(),
            'categories' => Category::withPostCounts(),
            'recent'     => array_slice(Post::allForAdmin(), 0, 5),
        ], 'layouts/admin');
    }
}
