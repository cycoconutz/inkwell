<?php

declare(strict_types=1);

use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\PostController as AdminPostController;
use App\Controllers\AuthController;
use App\Controllers\BlogController;
use App\Controllers\HealthController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Core\Router;

/*
 * Route table. Public pages, then auth, then the admin area. Verbs other than
 * GET/POST are sent by forms via a hidden _method field (see Request).
 */
return static function (Router $router): void {
    // Public
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/blog', [BlogController::class, 'index']);
    $router->get('/blog/{slug}', [BlogController::class, 'show']);
    $router->get('/about', [PageController::class, 'about']);
    $router->get('/healthz', [HealthController::class, 'index']);

    // Auth
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout']);

    // Admin dashboard
    $router->get('/admin', [DashboardController::class, 'index']);

    // Admin: posts
    $router->get('/admin/posts', [AdminPostController::class, 'index']);
    $router->get('/admin/posts/create', [AdminPostController::class, 'create']);
    $router->post('/admin/posts', [AdminPostController::class, 'store']);
    $router->get('/admin/posts/{id}/edit', [AdminPostController::class, 'edit']);
    $router->put('/admin/posts/{id}', [AdminPostController::class, 'update']);
    $router->delete('/admin/posts/{id}', [AdminPostController::class, 'destroy']);

    // Admin: categories
    $router->get('/admin/categories', [AdminCategoryController::class, 'index']);
    $router->post('/admin/categories', [AdminCategoryController::class, 'store']);
    $router->delete('/admin/categories/{id}', [AdminCategoryController::class, 'destroy']);
};
