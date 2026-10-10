<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Models\Category;
use App\Models\Post;

final class PostController extends AdminController
{
    public function index(Request $request): void
    {
        $this->view('admin/posts/index', [
            'posts' => Post::allForAdmin(),
        ], 'layouts/admin');
    }

    public function create(Request $request): void
    {
        $this->view('admin/posts/form', [
            'post'       => null,
            'categories' => Category::withPostCounts(),
        ], 'layouts/admin');
    }

    public function store(Request $request): void
    {
        $this->verifyCsrf($request);

        [$data, $errors] = $this->validated($request, null);

        if ($errors !== []) {
            $this->failed($request, $errors, url('admin/posts/create'));
        }

        $data['user_id'] = Auth::id();
        $post = Post::create($data);

        flash('success', 'Post created.');
        \App\Core\Session::remove('_old');
        $this->redirect(url('admin/posts/' . $post . '/edit'));
    }

    public function edit(Request $request, string $id): void
    {
        $post = Post::findWithRelations((int) $id);

        if ($post === null) {
            $this->abort(404);
        }

        $this->view('admin/posts/form', [
            'post'       => $post,
            'categories' => Category::withPostCounts(),
        ], 'layouts/admin');
    }

    public function update(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        $postId = (int) $id;

        if (Post::find($postId) === null) {
            $this->abort(404);
        }

        [$data, $errors] = $this->validated($request, $postId);

        if ($errors !== []) {
            $this->failed($request, $errors, url('admin/posts/' . $postId . '/edit'));
        }

        Post::update($postId, $data);

        flash('success', 'Post updated.');
        \App\Core\Session::remove('_old');
        $this->redirect(url('admin/posts/' . $postId . '/edit'));
    }

    public function destroy(Request $request, string $id): void
    {
        $this->verifyCsrf($request);

        Post::delete((int) $id);

        flash('success', 'Post deleted.');
        $this->redirect(url('admin/posts'));
    }

    /**
     * Validate and normalise the post form.
     *
     * @return array{0:array<string,mixed>, 1:array<string,string>}
     */
    private function validated(Request $request, ?int $ignoreId): array
    {
        $title   = $request->string('title');
        $body    = trim((string) $request->input('body', ''));
        $excerpt = $request->string('excerpt');
        $status  = $request->string('status', 'draft');
        $slugIn  = $request->string('slug');
        $catId   = $request->int('category_id', 0);
        $pubIn   = $request->string('published_at');

        $errors = [];

        if ($title === '') {
            $errors['title'] = 'A title is required.';
        }

        if ($body === '') {
            $errors['body'] = 'The body cannot be empty.';
        }

        if (!in_array($status, ['draft', 'published'], true)) {
            $status = 'draft';
        }

        if ($excerpt === '') {
            $excerpt = $this->excerptFrom($body);
        }

        $slug = $this->uniqueSlug($slugIn !== '' ? $slugIn : $title, $ignoreId);

        $publishedAt = null;
        if ($status === 'published') {
            $timestamp = $pubIn !== '' ? strtotime($pubIn) : time();
            $publishedAt = date('Y-m-d H:i:s', $timestamp !== false ? $timestamp : time());
        }

        $data = [
            'category_id'  => $catId > 0 ? $catId : null,
            'title'        => $title,
            'slug'         => $slug,
            'excerpt'      => $excerpt,
            'body'         => $body,
            'status'       => $status,
            'published_at' => $publishedAt,
        ];

        return [$data, $errors];
    }

    private function uniqueSlug(string $source, ?int $ignoreId): string
    {
        $base = str_slug($source);
        if ($base === '') {
            $base = 'untitled';
        }

        $slug = $base;
        $suffix = 2;

        while (Post::slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function excerptFrom(string $body): string
    {
        $plain = trim((string) preg_replace('/\s+/', ' ', strip_tags($body)));

        return mb_strlen($plain) > 160 ? mb_substr($plain, 0, 157) . '...' : $plain;
    }

    /**
     * @param array<string,string> $errors
     */
    private function failed(Request $request, array $errors, string $to): never
    {
        \App\Core\Session::set('_old', $request->only([
            'title', 'slug', 'excerpt', 'body', 'status', 'category_id', 'published_at',
        ]));
        flash('errors', $errors);
        $this->redirect($to);
    }
}
