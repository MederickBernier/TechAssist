<?php ob_start();
$err = \App\Flash::get('error'); ?>
<article>
    <h1>New Ticket</h1>
    <?php if ($err): ?><mark><?= htmlspecialchars($err) ?></mark><?php endif; ?>
    <form method="post" action="/?r=tickets/store">
        <?= \App\Csrf::field() ?>
        <label>Title <input name="title" maxlength="255" required></label>
        <label>Department
            <select name="department_id">
                <option value="">— Unassigned —</option>
                <?php foreach ($depts as $d): ?>
                    <option value="<?= (int)$d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Priority
            <select name="priority">
                <option value="normal">Normal</option>
                <option value="low">Low</option>
                <option value="high">High</option>
            </select>
        </label>
        <label>Description <textarea name="body" rows="6" required></textarea></label>
        <button>Create Ticket</button>
    </form>
</article>
<?php $content = ob_get_clean();
include __DIR__ . '/../layout.php'; ?>