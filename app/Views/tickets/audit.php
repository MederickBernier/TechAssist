<?php ob_start(); ?>
<article>
    <header style="display:flex;justify-content:space-between;align-items:center">
        <h1>Audit Log</h1>
        <a role="button" class="secondary" href="/?r=tickets">Back</a>
    </header>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>When</th>
                <th>User</th>
                <th>Action</th>
                <th>Ticket</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                    <td><?= htmlspecialchars($r['username'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['action']) ?></td>
                    <td><?= htmlspecialchars($r['ticket_id'] ?? '') ?></td>
                    <td>
                        <pre style="white-space:pre-wrap;margin:0"><?= htmlspecialchars($r['details'] ?? '') ?></pre>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</article>
<?php $content = ob_get_clean();
include __DIR__ . '/../layout.php'; ?>