<?php
page_title('Sign in');
?>
<h1 class="auth__title">Admin sign in</h1>
<p class="auth__subtitle">Use the credentials from your <code>.env</code> file.</p>

<form class="form" method="post" action="<?= e(url('login')) ?>">
    <?= csrf_field() ?>
    <div class="form__group">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= e(old('email')) ?>" autocomplete="username" required>
    </div>
    <div class="form__group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
    </div>
    <button class="btn btn--primary btn--full" type="submit">Sign in</button>
</form>
