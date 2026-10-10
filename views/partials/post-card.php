<?php /** @var array<string, mixed> $post */ ?>
<article class="card">
    <header class="card__head">
        <?php if (!empty($post['category_slug'])): ?>
            <a class="tag" href="<?= e(url('blog?category=' . $post['category_slug'])) ?>"><?= e((string) $post['category_name']) ?></a>
        <?php endif; ?>
        <h3 class="card__title">
            <a href="<?= e(url('blog/' . $post['slug'])) ?>"><?= e((string) $post['title']) ?></a>
        </h3>
        <p class="card__meta">
            <?= e(format_date($post['published_at'] ?? null)) ?>
            <span class="dot">&middot;</span>
            <?= (int) reading_time($post['body'] ?? '') ?> min read
        </p>
    </header>
    <p class="card__excerpt"><?= e((string) $post['excerpt']) ?></p>
    <a class="card__more" href="<?= e(url('blog/' . $post['slug'])) ?>" aria-label="Read <?= e((string) $post['title']) ?>">Read more &rarr;</a>
</article>
