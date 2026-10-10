<?php
/** @var array<string, mixed> $post */
/** @var array<int, array<string, mixed>> $related */
page_title((string) $post['title']);
page_meta('description', (string) $post['excerpt']);
?>
<article class="post">
    <header class="post__header">
        <?php if (!empty($post['category_slug'])): ?>
            <a class="tag" href="<?= e(url('blog?category=' . $post['category_slug'])) ?>"><?= e((string) $post['category_name']) ?></a>
        <?php endif; ?>
        <h1 class="post__title"><?= e((string) $post['title']) ?></h1>
        <p class="post__meta">
            <?= e(format_date($post['published_at'] ?? null)) ?>
            <span class="dot">&middot;</span>
            <?= e((string) ($post['author_name'] ?? 'Inkwell')) ?>
            <span class="dot">&middot;</span>
            <?= (int) reading_time($post['body'] ?? '') ?> min read
        </p>
    </header>

    <div class="post__body">
        <?= \App\Core\Markdown::toHtml((string) $post['body']) ?>
    </div>

    <footer class="post__footer">
        <a class="btn btn--ghost" href="<?= e(url('blog')) ?>">&larr; Back to writing</a>
    </footer>
</article>

<?php if ($related !== []): ?>
    <section class="section">
        <header class="section__head">
            <h2 class="section__title">More in this category</h2>
        </header>
        <div class="grid grid--two">
            <?php foreach ($related as $candidate): ?>
                <?php require base_path('views/partials/post-card.php'); ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
