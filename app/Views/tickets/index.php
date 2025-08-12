<?php ob_start();
$flash = \App\Flash::get(); ?>
<article>
    <header style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
        <h1>Tickets</h1>
        <a role="button" href="/?r=tickets/create">New</a>
    </header>

    <?php if ($flash): ?><mark><?= htmlspecialchars($flash) ?></mark><?php endif; ?>

    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <div style="text-align: right; margin-bottom: 1em;">
            <a href="/audit">View Audit Log</a>
        </div>
    <?php endif; ?>


    <form method="get" action="/" style="display:grid;grid-template-columns:repeat(4,minmax(120px,1fr));gap:0.5rem;margin:.5rem 0">
        <input type="hidden" name="r" value="tickets">
        <input name="q" placeholder="Search title/body..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <select name="status">
            <option value="">All statuses</option>
            <?php foreach (['open', 'closed'] as $s): ?>
                <option value="<?= $s ?>" <?= (($_GET['status'] ?? '') === $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="dept">
            <option value="">All depts</option>
            <?php foreach ($depts as $d): ?>
                <option value="<?= (int)$d['id'] ?>" <?= ((string)($d['id']) === ($_GET['dept'] ?? '')) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button>Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Dept</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Assignee</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $t): ?>
                <tr>
                    <td><?= (int)$t['id'] ?></td>
                    <td><a href="/?r=tickets/view&id=<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['title']) ?></a></td>
                    <td><?= htmlspecialchars($t['dept'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($t['priority']) ?></td>
                    <td><?= htmlspecialchars($t['status']) ?></td>
                    <td><?= htmlspecialchars($t['assignee'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($t['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($pages > 1): ?>
        <nav aria-label="pagination" style="display:flex;gap:.5rem;flex-wrap:wrap">
            <?php for ($i = 1; $i <= $pages; $i++): $params = $_GET;
                $params['page'] = $i; ?>
                <?php if ($i === (int)($_GET['page'] ?? 1)): ?>
                    <strong><?= $i ?></strong>
                <?php else: ?>
                    <a href="/?<?= http_build_query($params) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</article>
<?php $content = ob_get_clean();
include __DIR__ . '/../layout.php'; ?>