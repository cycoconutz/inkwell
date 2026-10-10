<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Category;
use App\Models\Post;

final class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $this->view('home', [
            'posts'      => Post::latest(3),
            'categories' => Category::withPostCounts(),
            'total'      => Post::publishedCount(),
        ]);
    }
}
