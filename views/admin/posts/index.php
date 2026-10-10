<?php
/** @var array<int, array<string, mixed>> $posts */
page_title('Posts');
?>
<header class="admin-header">
    <div>
        <h1 class="admin-title">Posts</h1>
        <p class="muted">Publish, edit or retire entries.</p>
    </div>
    <a class="btn btn--primary" href="<?= e(url('admin/posts/create')) ?>">New post</a>
</header>

<div class="panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <a class="table__link" href="<?= e(url('admin/posts/' . $post['id'] . '/edit')) ?>"><?= e($post['title']) ?></a>
                            <span class="table__sub">/blog/<?= e($post['slug']) ?></span>
                        </td>
                        <td><span class="pill pill--<?= e($post['status']) ?>"><?= e($post['status']) ?></span></td>
                        <td><?= e($post['category_name'] ?? 'Uncategorised') ?></td>
                        <td><?= e($post['published_at'] ? format_date($post['published_at'], 'Y-m-d H:i') : '-') ?></td>
                        <td class="table__actions">
                            <a class="btn btn--ghost btn--small" href="<?= e(url('blog/' . $post['slug'])) ?>" target="_blank" rel="noopener">View</a>
                            <a class="btn btn--ghost btn--small" href="<?= e(url('admin/posts/' . $post['id'] . '/edit')) ?>">Edit</a>
                            <form method="post" action="<?= e(url('admin/posts/' . $post['id'])) ?>" onsubmit="return confirm('Delete this post?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="delete">
                                <button class="btn btn--danger btn--small" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
