<?php
/** @var array<string, mixed>|null $post */
/** @var array<int, array<string, mixed>> $categories */
page_title($post === null ? 'New post' : 'Edit post');
$errors = \App\Core\Session::pullFlash('errors');
if (!is_array($errors)) {
    $errors = [];
}
?>
<header class="admin-header">
    <div>
        <h1 class="admin-title"><?= $post === null ? 'New post' : 'Edit post' ?></h1>
        <p class="muted">Plain text and basic Markdown work best.</p>
    </div>
    <?php if ($post !== null): ?>
        <a class="btn btn--ghost" href="<?= e(url('blog/' . $post['slug'])) ?>" target="_blank" rel="noopener">Preview live</a>
    <?php endif; ?>
</header>

<form class="form form--split" method="post" action="<?= $post === null ? e(url('admin/posts')) : e(url('admin/posts/' . $post['id'])) ?>">
    <?= csrf_field() ?>
    <?php if ($post !== null): ?>
        <input type="hidden" name="_method" value="put">
    <?php endif; ?>

    <div class="form__main">
        <div class="panel">
            <div class="form__group">
                <label for="title">Title</label>
                <input id="title" name="title" value="<?= e(old('title', (string) ($post['title'] ?? ''))) ?>" required>
                <?php if (!empty($errors['title'])): ?><p class="field-error"><?= e($errors['title']) ?></p><?php endif; ?>
            </div>
            <div class="form__group">
                <label for="slug">Slug (optional)</label>
                <input id="slug" name="slug" value="<?= e(old('slug', (string) ($post['slug'] ?? ''))) ?>" placeholder="auto-generated from title">
            </div>
            <div class="form__group">
                <label for="excerpt">Excerpt (optional)</label>
                <textarea id="excerpt" name="excerpt" rows="3" placeholder="Leave blank to generate from body"><?= e(old('excerpt', (string) ($post['excerpt'] ?? ''))) ?></textarea>
            </div>
            <div class="form__group">
                <label for="body">Body</label>
                <textarea id="body" name="body" rows="18" required><?= e(old('body', (string) ($post['body'] ?? ''))) ?></textarea>
                <?php if (!empty($errors['body'])): ?><p class="field-error"><?= e($errors['body']) ?></p><?php endif; ?>
                <p class="muted">Supports headings, lists, blockquotes, fenced <code>```</code> code, inline <code>`code`</code>, <strong>**bold**</strong>, <em>*italic*</em> and links.</p>
            </div>
        </div>
    </div>

    <aside class="form__aside">
        <div class="panel">
            <div class="form__group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft" <?= old('status', (string) ($post['status'] ?? 'draft')) === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= old('status', (string) ($post['status'] ?? 'draft')) === 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>
            <div class="form__group">
                <label for="published_at">Publish date (leave blank to use now)</label>
                <input id="published_at" name="published_at" type="datetime-local" value="<?= e(old('published_at', (string) ($post['published_at'] ?? ''))) ?>">
            </div>
            <div class="form__group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="0">Uncategorised</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php $current = (int) old('category_id', (int) ($post['category_id'] ?? 0)); ?>
                        <option value="<?= (int) $cat['id'] ?>" <?= $current === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn--primary btn--full" type="submit"><?= $post === null ? 'Create post' : 'Save changes' ?></button>
            <a class="btn btn--ghost btn--full" href="<?= e(url('admin/posts')) ?>">Cancel</a>
        </div>
    </aside>
</form>
