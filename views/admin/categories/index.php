<?php
/** @var array<int, array<string, mixed>> $categories */
page_title('Categories');
$errors = \App\Core\Session::pullFlash('errors');
if (!is_array($errors)) {
    $errors = [];
}
$error = \App\Core\Session::pullFlash('error');
if (is_string($error) && $error !== '') {
    $errors[] = $error;
}
?>
<header class="admin-header">
    <div>
        <h1 class="admin-title">Categories</h1>
        <p class="muted">Group your writing for easier discovery.</p>
    </div>
</header>

<div class="grid grid--two">
    <div class="panel">
        <h2 class="panel__title">Add a category</h2>
        <form class="form" method="post" action="<?= e(url('admin/categories')) ?>">
            <?= csrf_field() ?>
            <div class="form__group">
                <label for="name">Name</label>
                <input id="name" name="name" required>
            </div>
            <div class="form__group">
                <label for="description">Description (optional)</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            <button class="btn btn--primary" type="submit">Add category</button>
        </form>
    </div>

    <div class="panel">
        <h2 class="panel__title">Existing categories</h2>
        <?php if ($categories === []): ?>
            <p class="muted">No categories yet.</p>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($categories as $cat): ?>
                    <li class="list__item">
                        <div>
                            <span class="list__title"><?= e($cat['name']) ?></span>
                            <span class="list__meta"><?= (int) $cat['post_count'] ?> post<?= (int) $cat['post_count'] === 1 ? '' : 's' ?></span>
                        </div>
                        <form method="post" action="<?= e(url('admin/categories/' . $cat['id'])) ?>" onsubmit="return confirm('Remove this category? Posts will become uncategorised.');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="delete">
                            <button class="btn btn--danger btn--small" type="submit">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
