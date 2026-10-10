<?php
/**
 * @var array<int, array<string, mixed>> $posts
 * @var array<int, array<string, mixed>> $categories
 * @var int $total
 */
page_title('Inkwell');
page_meta('description', 'A small, self-contained PHP blog and publishing engine.');
?>
<section class="hero">
    <div class="hero__content">
        <h1 class="hero__title">Notes worth keeping.</h1>
        <p class="hero__text">
            Inkwell is a plain PHP, zero-dependency CMS scaffold: Markdown-friendly, safe from injection,
            and easy to deploy to a single Docker container.
        </p>
        <div class="hero__actions">
            <a class="btn btn--primary" href="<?= e(url('blog')) ?>">Start reading</a>
            <a class="btn btn--ghost" href="<?= e(url('about')) ?>">About this project</a>
        </div>
    </div>
</section>

<section class="section">
    <header class="section__head">
        <h2 class="section__title">Latest posts</h2>
        <a class="section__link" href="<?= e(url('blog')) ?>">All writing &rarr;</a>
    </header>
    <div class="grid grid--three">
        <?php if ($posts === []): ?>
            <p class="muted">No published posts yet. Create one in the admin area.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?php require base_path('views/partials/post-card.php'); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php if ($categories !== []): ?>
    <section class="section">
        <header class="section__head">
            <h2 class="section__title">Explore by category</h2>
        </header>
        <div class="chips">
            <?php foreach ($categories as $category): ?>
                <a class="chip" href="<?= e(url('blog?category=' . $category['slug'])) ?>">
                    <?= e($category['name']) ?>
                    <span class="chip__count"><?= (int) $category['post_count'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
