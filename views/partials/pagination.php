<?php
/**
 * @var int $page
 * @var int $pages
 * @var string $term
 * @var array<string, mixed>|null $category
 */
if ($pages <= 1) {
    return;
}
?>
<nav class="pagination" aria-label="Pagination">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
        <?php
        $query = array_filter([
            'q'        => $term ?? '',
            'category' => $category['slug'] ?? '',
            'page'     => $p > 1 ? $p : null,
        ]);
        $href = url('blog') . ($query !== [] ? '?' . http_build_query($query) : '');
        ?>
        <a class="pagination__link<?= $p === $page ? ' is-current' : '' ?>"
           href="<?= e($href) ?>"
           <?= $p === $page ? 'aria-current="page"' : '' ?>><?= $p ?></a>
    <?php endfor; ?>
</nav>
