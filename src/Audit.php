<?php

namespace App;

use App\DB;
use PDO;

final class Audit
{
    public function audit(): string
    {
        if (!\App\Auth::check()) {
            header('Location: /?r=login');
            exit;
        }
        $pdo = \App\DB::pdo();
        $rows = $pdo->query("
    SELECT a.id, a.action, a.ticket_id, a.details, a.created_at, u.username
    FROM audit_log a LEFT JOIN users u ON u.id=a.user_id
    ORDER BY a.id DESC LIMIT 200
  ")->fetchAll();
        ob_start();
        $title = 'Audit Log';
        include __DIR__ . '/../Views/tickets/audit.php';
        return ob_get_clean();
    }


    public static function log(string $action, ?int $ticketId = null, array $details = []): void
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO audit_log(user_id, action, ticket_id, details) VALUES(?,?,?,JSON_OBJECT())");
        // MySQL’s JSON_OBJECT() needs pairs; we’ll encode in PHP instead:
        $stmt = $pdo->prepare("INSERT INTO audit_log(user_id, action, ticket_id, details) VALUES(?,?,?,?)");
        $uid = $_SESSION['user']['id'] ?? null;
        $stmt->execute([$uid, $action, $ticketId, json_encode($details, JSON_UNESCAPED_UNICODE)]);
    }
}
