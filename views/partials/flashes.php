<?php
/** @var string|null $success */
/** @var string|null $error */
$success = \App\Core\Session::pullFlash('success');
$error   = \App\Core\Session::pullFlash('error');
?>
<?php if (is_string($success) && $success !== ''): ?>
    <div class="flash flash--success" role="status"><?= e($success) ?></div>
<?php endif; ?>
<?php if (is_string($error) && $error !== ''): ?>
    <div class="flash flash--error" role="alert"><?= e($error) ?></div>
<?php endif; ?>
