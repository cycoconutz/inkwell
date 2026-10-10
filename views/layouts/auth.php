<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(page_title()) ?></title>
    <link rel="icon" href="<?= e(asset('favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="body--auth">
    <main class="auth">
        <div class="auth__brand">
            <a href="<?= e(url('/')) ?>"><?= e(config('app.name', 'Inkwell')) ?><span class="brand__dot">.</span></a>
        </div>
        <div class="auth__card">
            <?php require base_path('views/partials/flashes.php'); ?>
            <?= $content ?>
        </div>
        <p class="auth__foot"><a href="<?= e(url('/')) ?>">&larr; Back to the site</a></p>
    </main>
</body>
</html>
