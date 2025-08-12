<?php
$title = $title ?? 'TechAssist';
$u = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title) ?> · TechAssist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css" rel="stylesheet">
</head>

<body>
    <nav class="container-fluid">
        <ul>
            <li><strong>TechAssist</strong></li>
        </ul>
        <ul>
            <?php if ($u): ?>
                <li>Hello, <?= htmlspecialchars($u['username']) ?></li>
                <li><a href="/?r=logout">Logout</a></li>
            <?php else: ?>
                <li><a href="/?r=login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main class="container">
        <?= $content ?? '' ?>
    </main>
</body>

</html>