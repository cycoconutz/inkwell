<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Models\Category;

final class CategoryController extends AdminController
{
    public function index(Request $request): void
    {
        $this->view('admin/categories/index', [
            'categories' => Category::withPostCounts(),
        ], 'layouts/admin');
    }

    public function store(Request $request): void
    {
        $this->verifyCsrf($request);

        $name        = $request->string('name');
        $description = $request->string('description');

        if ($name === '') {
            flash('error', 'A category name is required.');
            $this->redirect(url('admin/categories'));
        }

        if (Category::findBySlug(str_slug($name)) !== null) {
            flash('error', 'That category already exists.');
            $this->redirect(url('admin/categories'));
        }

        Category::create($name, $description);

        flash('success', 'Category added.');
        $this->redirect(url('admin/categories'));
    }

    public function destroy(Request $request, string $id): void
    {
        $this->verifyCsrf($request);

        Category::delete((int) $id);

        flash('success', 'Category removed. Its posts are now uncategorised.');
        $this->redirect(url('admin/categories'));
    }
}
