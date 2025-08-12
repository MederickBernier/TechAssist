<?php ob_start(); ?>
<article>
    <h1>Login</h1>
    <?php if (!empty($error)): ?><mark><?= htmlspecialchars($error) ?></mark><?php endif; ?>
    <form method="post" action="/?r=login">
        <?= \App\Csrf::field() ?>
        <label>Username <input name="username" required autofocus></label>
        <label>Password <input type="password" name="password" required></label>
        <button>Sign in</button>
    </form>
</article>
<?php $content = ob_get_clean();
include __DIR__ . '/layout.php'; ?>