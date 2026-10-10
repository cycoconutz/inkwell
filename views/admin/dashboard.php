<?php
/** @var array<string, mixed> $user */
/** @var array<string, mixed> $stats */
/** @var array<int, array<string, mixed>> $recent */
page_title('Dashboard');
?>
<h1 class="admin-title">Dashboard</h1>

<div class="stats">
    <div class="stat">
        <span class="stat__value"><?= (int) $stats['total'] ?></span>
        <span class="stat__label">Total posts</span>
    </div>
    <div class="stat">
        <span class="stat__value"><?= (int) $stats['published'] ?></span>
        <span class="stat__label">Published</span>
    </div>
    <div class="stat">
        <span class="stat__value"><?= (int) $stats['draft'] ?></span>
        <span class="stat__label">Drafts</span>
    </div>
</div>

<div class="panel">
    <header class="panel__head">
        <h2 class="panel__title">Recent posts</h2>
        <a class="btn btn--ghost btn--small" href="<?= e(url('admin/posts/create')) ?>">New post</a>
    </header>
    <?php if ($recent === []): ?>
        <p class="muted">No posts yet. <a href="<?= e(url('admin/posts/create')) ?>">Write your first post</a>.</p>
    <?php else: ?>
        <ul class="list">
            <?php foreach ($recent as $p): ?>
                <li class="list__item">
                    <div>
                        <a class="list__title" href="<?= e(url('admin/posts/' . $p['id'] . '/edit')) ?>"><?= e($p['title']) ?></a>
                        <span class="list__meta"><?= e(strtoupper($p['status'])) ?> &middot; <?= e(format_date($p['updated_at'] ?? null, 'Y-m-d H:i')) ?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
