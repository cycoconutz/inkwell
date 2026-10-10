<?php
/**
 * @var array<int, array<string, mixed>> $posts
 * @var array<int, array<string, mixed>> $categories
 * @var int $page
 * @var int $pages
 * @var string $term
 * @var array<string, mixed>|null $category
 * @var int $total
 */
page_title($category ? $category['name'] . ' - Writing' : ($term !== '' ? 'Search results for "' . $term . '"' : 'Writing'));
page_meta('description', 'Archive of posts on Inkwell.');
?>
<header class="page-header">
    <h1 class="page-header__title">
        <?php if ($category): ?>
            <?= e($category['name']) ?>
        <?php elseif ($term !== ''): ?>
            Results for "<?= e($term) ?>"
        <?php else: ?>
            Writing
        <?php endif; ?>
    </h1>
    <p class="page-header__text"><?= (int) $total ?> post<?= $total === 1 ? '' : 's' ?> found.</p>
</header>

<form class="search" method="get" action="<?= e(url('blog')) ?>">
    <label class="search__label" for="q">Search</label>
    <input class="search__input" id="q" name="q" type="search" value="<?= e($term) ?>" placeholder="Search titles, excerpts or text...">
    <button class="btn btn--primary" type="submit">Search</button>
</form>

<div class="with-sidebar">
    <aside class="sidebar">
        <h2 class="sidebar__title">Categories</h2>
        <ul class="sidebar__list">
            <li>
                <a class="sidebar__link<?= $category === null ? ' is-active' : '' ?>" href="<?= e(url('blog')) ?>">All posts</a>
            </li>
            <?php foreach ($categories as $cat): ?>
                <li>
                    <a class="sidebar__link<?= $category && $category['slug'] === $cat['slug'] ? ' is-active' : '' ?>"
                       href="<?= e(url('blog?category=' . $cat['slug'])) ?>">
                        <?= e($cat['name']) ?> (<?= (int) $cat['post_count'] ?>)
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <div class="feed">
        <?php if ($posts === []): ?>
            <div class="empty">
                <h3>No posts to show</h3>
                <p>Try adjusting your search or return to the <a href="<?= e(url('blog')) ?>">full archive</a>.</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?php require base_path('views/partials/post-card.php'); ?>
            <?php endforeach; ?>
            <?php require base_path('views/partials/pagination.php'); ?>
        <?php endif; ?>
    </div>
</div>
