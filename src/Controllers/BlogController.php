<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Category;
use App\Models\Post;

final class BlogController extends Controller
{
    public function index(Request $request): void
    {
        $perPage = (int) config('pagination.per_page', 6);
        $page    = max(1, $request->int('page', 1));
        $offset  = ($page - 1) * $perPage;
        $term    = $request->string('q');
        $slug    = $request->string('category');

        $category = $slug !== '' ? Category::findBySlug($slug) : null;

        if ($term !== '') {
            $posts = Post::search($term, $perPage, $offset);
            $total = Post::searchCount($term);
        } elseif ($category !== null) {
            $id = (int) $category['id'];
            $posts = Post::publishedByCategory($id, $perPage, $offset);
            $total = Post::publishedCountByCategory($id);
        } else {
            $posts = Post::published($perPage, $offset);
            $total = Post::publishedCount();
        }

        $this->view('blog/index', [
            'posts'      => $posts,
            'categories' => Category::withPostCounts(),
            'category'   => $category,
            'term'       => $term,
            'page'       => $page,
            'pages'      => max(1, (int) ceil($total / $perPage)),
            'total'      => $total,
        ]);
    }

    public function show(Request $request, string $slug): void
    {
        $post = Post::findPublishedBySlug($slug);

        if ($post === null) {
            $this->abort(404);
        }

        $related = [];

        if (!empty($post['category_id'])) {
            foreach (Post::publishedByCategory((int) $post['category_id'], 4) as $candidate) {
                if ((int) $candidate['id'] !== (int) $post['id']) {
                    $related[] = $candidate;
                }
            }
        }

        $this->view('blog/show', [
            'post'    => $post,
            'related' => array_slice($related, 0, 2),
        ]);
    }
}
