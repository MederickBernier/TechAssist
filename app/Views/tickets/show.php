<?php ob_start();
$flash = \App\Flash::get();
$isAdmin = (($_SESSION['user']['role'] ?? 'user') === 'admin'); ?>
<article>
    <header style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
        <h1>Ticket #<?= (int)$t['id'] ?> — <?= htmlspecialchars($t['title']) ?></h1>
        <a href="/?r=tickets" role="button" class="secondary">Back</a>
    </header>

    <?php if ($flash): ?><mark><?= htmlspecialchars($flash) ?></mark><?php endif; ?>

    <ul>
        <li><strong>Status:</strong> <?= htmlspecialchars($t['status']) ?></li>
        <li><strong>Priority:</strong> <?= htmlspecialchars($t['priority']) ?></li>
        <li><strong>Department:</strong> <?= htmlspecialchars($t['dept'] ?? '-') ?></li>
        <li><strong>Author:</strong> <?= htmlspecialchars($t['author'] ?? '-') ?></li>
        <li><strong>Assignee:</strong> <?= htmlspecialchars($t['assignee'] ?? '-') ?></li>
        <li><strong>Created:</strong> <?= htmlspecialchars($t['created_at']) ?></li>
        <?php if ($t['closed_at']): ?><li><strong>Closed:</strong> <?= htmlspecialchars($t['closed_at']) ?></li><?php endif; ?>
    </ul>

    <details open>
        <summary>Description</summary>
        <pre><?= htmlspecialchars($t['body']) ?></pre>
    </details>

    <div style="display:flex;gap:.5rem;margin:.5rem 0">
        <?php if ($t['status'] !== 'closed'): ?>
            <form method="post" action="/?r=tickets/close">
                <?= \App\Csrf::field() ?>
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button>Close</button>
            </form>
        <?php else: ?>
            <form method="post" action="/?r=tickets/reopen">
                <?= \App\Csrf::field() ?>
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button>Reopen</button>
            </form>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
            <form method="post" action="/?r=tickets/assign">
                <?= \App\Csrf::field() ?>
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <select name="assignee_id">
                    <option value="">— Unassigned —</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= (int)$u['id'] ?>" <?= ((int)$u['id']) === (int)($t['assignee_id'] ?? 0) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button>Assign</button>
            </form>
        <?php endif; ?>
    </div>

    <hr>
    <h3>Comments</h3>
    <ul>
        <?php foreach ($comments as $c): ?>
            <li><strong><?= htmlspecialchars($c['username']) ?></strong> — <small><?= htmlspecialchars($c['created_at']) ?></small><br><?= nl2br(htmlspecialchars($c['body'])) ?></li>
        <?php endforeach; ?>
        <?php if (!$comments): ?><li><em>No comments yet.</em></li><?php endif; ?>
    </ul>

    <form method="post" action="/?r=tickets/comment">
        <?= \App\Csrf::field() ?>
        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
        <label>Add a comment
            <textarea name="body" rows="4" required></textarea>
        </label>
        <button>Add comment</button>
    </form>
</article>
<?php $content = ob_get_clean();
include __DIR__ . '/../layout.php'; ?>