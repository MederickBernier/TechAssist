<?php

namespace App\Controllers;

use App\Auth;
use App\Csrf;
use App\DB;
use App\Flash;
use App\Audit;
use PDO;

final class TicketController
{
    // list with filters + pagination
    public function index(): string
    {
        if (!Auth::check()) {
            header('Location: /?r=login');
            exit;
        }
        $pdo = DB::pdo();

        $status = $_GET['status'] ?? '';
        $dept   = isset($_GET['dept']) && $_GET['dept'] !== '' ? (int)$_GET['dept'] : null;
        $q      = trim($_GET['q'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $per    = 10;
        $off    = ($page - 1) * $per;

        $where = [];
        $args  = [];
        if ($status !== '') {
            $where[] = 't.status = ?';
            $args[] = $status;
        }
        if ($dept) {
            $where[] = 't.department_id = ?';
            $args[] = $dept;
        }
        if ($q !== '') {
            $where[] = '(t.title LIKE ? OR t.body LIKE ?)';
            $args[] = "%$q%";
            $args[] = "%$q%";
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets t $whereSql");
        $stmt->execute($args);
        $total = (int)$stmt->fetchColumn();

        // page data
        $sql = "
      SELECT t.id, t.title, t.status, t.priority, t.created_at, d.name AS dept, u.username AS assignee
      FROM tickets t
      LEFT JOIN departments d ON d.id = t.department_id
      LEFT JOIN users u ON u.id = t.assignee_id
      $whereSql
      ORDER BY t.created_at DESC
      LIMIT $per OFFSET $off
    ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);
        $tickets = $stmt->fetchAll();

        $depts = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll();

        $pages = (int)ceil(max(1, $total) / $per);

        ob_start();
        $title = 'Tickets';
        include __DIR__ . '/../Views/tickets/index.php';
        return ob_get_clean();
    }

    public function create(): string
    {
        if (!Auth::check()) {
            header('Location: /?r=login');
            exit;
        }
        $pdo = DB::pdo();
        $depts = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll();
        ob_start();
        $title = 'New Ticket';
        include __DIR__ . '/../Views/tickets/create.php';
        return ob_get_clean();
    }

    public function store(): void
    {
        if (!Auth::check()) {
            header('Location: /?r=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
        Csrf::check();

        $title = trim($_POST['title'] ?? '');
        $body  = trim($_POST['body'] ?? '');
        $dept  = ($_POST['department_id'] ?? '') !== '' ? (int)$_POST['department_id'] : null;
        $priority = $_POST['priority'] ?? 'normal';

        if ($title === '' || $body === '') {
            Flash::set('error', 'Title and description are required.');
            header('Location: /?r=tickets/create');
            exit;
        }

        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO tickets(user_id, department_id, title, body, status, priority) VALUES(?,?,?,?, 'open', ?)");
        $stmt->execute([$_SESSION['user']['id'], $dept, $title, $body, $priority]);

        $id = (int)$pdo->lastInsertId();
        Audit::log('ticket.create', $id, ['title' => $title, 'priority' => $priority]);
        Flash::set('success', 'Ticket created.');
        header("Location: /?r=tickets/view&id=$id");
        exit;
    }

    // show details + comments
    public function show(): string
    {
        if (!Auth::check()) {
            header('Location: /?r=login');
            exit;
        }
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(404);
            exit('Not Found');
        }

        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
      SELECT t.*, d.name AS dept, ua.username AS author, us.username AS assignee
      FROM tickets t
      LEFT JOIN departments d ON d.id=t.department_id
      LEFT JOIN users ua ON ua.id=t.user_id
      LEFT JOIN users us ON us.id=t.assignee_id
      WHERE t.id=?
    ");
        $stmt->execute([$id]);
        $t = $stmt->fetch();
        if (!$t) {
            http_response_code(404);
            exit('Not Found');
        }

        $stmt = $pdo->prepare("
      SELECT c.body, c.created_at, u.username
      FROM ticket_comments c JOIN users u ON u.id = c.user_id
      WHERE c.ticket_id=? ORDER BY c.created_at ASC
    ");
        $stmt->execute([$id]);
        $comments = $stmt->fetchAll();

        $users = $pdo->query("SELECT id, username FROM users ORDER BY username")->fetchAll();

        ob_start();
        $title = 'Ticket #' . $id;
        include __DIR__ . '/../Views/tickets/show.php';
        return ob_get_clean();
    }

    public function comment(): void
    {
        if (!Auth::check()) {
            header('Location: /?r=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
        Csrf::check();
        $id = (int)($_POST['id'] ?? 0);
        $body = trim($_POST['body'] ?? '');
        if ($id <= 0 || $body === '') {
            Flash::set('error', 'Empty comment.');
            header("Location: /?r=tickets/view&id=$id");
            exit;
        }

        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO ticket_comments(ticket_id, user_id, body) VALUES(?,?,?)");
        $stmt->execute([$id, $_SESSION['user']['id'], $body]);

        Audit::log('ticket.comment', $id, ['len' => strlen($body)]);
        Flash::set('success', 'Comment added.');
        header("Location: /?r=tickets/view&id=$id");
        exit;
    }

    public function close(): void
    {
        if (!Auth::check()) {
            header('Location: /?r=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
        Csrf::check();
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /');
            exit;
        }
        $pdo = DB::pdo();
        $pdo->prepare("UPDATE tickets SET status='closed', closed_at=NOW() WHERE id=?")->execute([$id]);
        Audit::log('ticket.close', $id);
        Flash::set('success', 'Ticket closed.');
        header("Location: /?r=tickets/view&id=$id");
        exit;
    }

    public function reopen(): void
    {
        if (!Auth::check()) {
            header('Location: /?r=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
        Csrf::check();
        $id = (int)($_POST['id'] ?? 0);
        $pdo = DB::pdo();
        $pdo->prepare("UPDATE tickets SET status='open', closed_at=NULL WHERE id=?")->execute([$id]);
        Audit::log('ticket.reopen', $id);
        Flash::set('success', 'Ticket reopened.');
        header("Location: /?r=tickets/view&id=$id");
        exit;
    }

    // admin-only assignment
    public function assign(): void
    {
        if (!Auth::check()) {
            header('Location: /?r=login');
            exit;
        }
        if (($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            http_response_code(403);
            exit('Forbidden');
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }
        Csrf::check();

        $id = (int)($_POST['id'] ?? 0);
        $assignee = (int)($_POST['assignee_id'] ?? 0);
        $pdo = DB::pdo();
        $pdo->prepare("UPDATE tickets SET assignee_id=? WHERE id=?")->execute([$assignee, $id]);
        Audit::log('ticket.assign', $id, ['assignee' => $assignee]);
        Flash::set('success', 'Assignee updated.');
        header("Location: /?r=tickets/view&id=$id");
        exit;
    }

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
}
