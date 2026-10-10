<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(page_title()) ?> &middot; Admin</title>
    <link rel="icon" href="<?= e(asset('favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="body--admin">
    <a class="skip-link" href="#admin-main">Skip to content</a>

    <div class="admin">
        <aside class="admin__aside">
            <a class="brand brand--admin" href="<?= e(url('/')) ?>"><?= e(config('app.name', 'Inkwell')) ?><span class="brand__dot">.</span></a>
            <nav class="admin-nav" aria-label="Admin">
                <a class="admin-nav__link<?= e(nav_active('/admin', true)) ?>" href="<?= e(url('admin')) ?>">Dashboard</a>
                <a class="admin-nav__link<?= e(nav_active('/admin/posts')) ?>" href="<?= e(url('admin/posts')) ?>">Posts</a>
                <a class="admin-nav__link<?= e(nav_active('/admin/categories')) ?>" href="<?= e(url('admin/categories')) ?>">Categories</a>
                <a class="admin-nav__link" href="<?= e(url('/')) ?>" target="_blank" rel="noopener">View site &nearr;</a>
            </nav>
            <form class="admin__logout" method="post" action="<?= e(url('logout')) ?>">
                <?= csrf_field() ?>
                <button class="btn btn--ghost btn--small" type="submit">Sign out</button>
            </form>
        </aside>

        <div class="admin__body">
            <header class="admin__topbar">
                <span class="admin__user">Signed in as <strong><?= e((string) (\App\Core\Auth::user()['name'] ?? '')) ?></strong></span>
            </header>

            <main id="admin-main" class="admin__main">
                <?php require base_path('views/partials/flashes.php'); ?>
                <?= $content ?>
            </main>
        </div>
    </div>

    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
