<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(page_title()) ?></title>
    <?php if (page_meta('description') !== ''): ?>
        <meta name="description" content="<?= e(page_meta('description')) ?>">
    <?php endif; ?>
    <link rel="icon" href="<?= e(asset('favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header">
        <div class="wrap site-header__inner">
            <a class="brand" href="<?= e(url('/')) ?>"><?= e(config('app.name', 'Inkwell')) ?><span class="brand__dot">.</span></a>

            <nav class="nav" aria-label="Primary">
                <a class="nav__link<?= e(nav_active('/')) ?>" href="<?= e(url('/')) ?>">Home</a>
                <a class="nav__link<?= e(nav_active('/blog')) ?>" href="<?= e(url('blog')) ?>">Writing</a>
                <a class="nav__link<?= e(nav_active('/about')) ?>" href="<?= e(url('about')) ?>">About</a>
                <a class="nav__link<?= e(nav_active('/admin')) ?>" href="<?= e(\App\Core\Auth::check() ? url('admin') : url('login')) ?>">
                    <?= \App\Core\Auth::check() ? 'Dashboard' : 'Sign in' ?>
                </a>
            </nav>
        </div>
    </header>

    <main id="main" class="wrap main">
        <?php require base_path('views/partials/flashes.php'); ?>
        <?= $content /* already-rendered, escaped by each view */ ?>
    </main>

    <footer class="site-footer">
        <div class="wrap site-footer__inner">
            <p>&copy; <?= e(date('Y')) ?> <?= e(config('app.name', 'Inkwell')) ?>. A small, self-contained PHP publishing engine.</p>
            <p class="site-footer__meta">
                <a href="<?= e(url('blog')) ?>">Writing</a>
                <a href="<?= e(url('about')) ?>">About</a>
                <a href="<?= e(url('healthz')) ?>">Status</a>
            </p>
        </div>
    </footer>

    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
