<?php
session_start();
require 'config.php';

$allowed = ['Chairperson', 'Secretary', 'Assistant Secretary', 'dev'];
if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], $allowed)) {
    header('Location: profile.php'); exit;
}

$admin_id = $_SESSION['member_id'];
$success = $error = '';

// Handle new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $priority = $_POST['priority'] ?? 'normal';
    if ($title && $message) {
        $stmt = $pdo->prepare("INSERT INTO announcements (admin_id, title, message, priority) VALUES (?, ?, ?, ?)");
        $stmt->execute([$admin_id, $title, $message, $priority]);
        log_admin_activity($pdo, "Announcement Created", "Title: $title, Priority: $priority");
        $success = "Announcement published successfully!";
        // Notify all paid members (in-app + email)
        try {
            require_once __DIR__ . '/includes/NotificationService.php';
            $members = $pdo->query("SELECT id FROM members WHERE paid = 1")->fetchAll(PDO::FETCH_ASSOC);
            $msg = nl2br(htmlspecialchars($message));
            foreach ($members as $m) {
                try {
                    NotificationService::notify((int)$m['id'], $title, $msg, 'announcement');
                } catch (Exception $e) {
                    error_log("Announcement notify member {$m['id']}: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            error_log("Announcement notifications: " . $e->getMessage());
        }
    } else {
        $error = "Title and message are required.";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT title FROM announcements WHERE id = ?");
    $stmt->execute([$del_id]);
    $ann = $stmt->fetch();
    if ($ann) {
        $pdo->prepare("DELETE FROM announcements WHERE id = ?")->execute([$del_id]);
        log_admin_activity($pdo, "Announcement Deleted", "Title: {$ann['title']}");
        $success = "Announcement deleted.";
    }
}

// Fetch all announcements
$announcements = $pdo->query("SELECT a.*, m.name as author FROM announcements a LEFT JOIN members m ON a.admin_id = m.id ORDER BY a.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Announcements  Jera Moyie</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root { --primary: #1a5928; --bg: #f3f4f6; }
body { background: var(--bg); font-family: 'Inter', sans-serif; padding-top: 30px; }
.glass-card { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.priority-urgent { border-left: 4px solid #dc3545; }
.priority-important { border-left: 4px solid #f59e0b; }
.priority-normal { border-left: 4px solid #6c757d; }
</style>
</head>
<body>
<div class="container mb-5">
<div class="d-flex justify-content-between align-items-center mb-4">
<div><h2 class="fw-bold mb-0"><i class="bi bi-megaphone me-2"></i>Announcements</h2><p class="text-muted small">Broadcast messages to all members</p></div>
<div class="d-flex gap-2">
    <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-pill px-4 btn-sm"><i class="bi bi-chevron-left me-1"></i>Console</a>
    <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">Profile</a>
</div>
</div>

<?php if ($success): ?><div class="alert alert-success border-0 shadow-sm rounded-4 small py-2"><i class="bi bi-check-circle me-2"></i><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger border-0 shadow-sm rounded-4 small py-2"><i class="bi bi-exclamation-triangle me-2"></i><?= $error ?></div><?php endif; ?>

<div class="glass-card p-4 mb-4">
<h5 class="fw-bold mb-3">New Announcement</h5>
<form method="POST">
<div class="mb-3"><label class="form-label small fw-bold text-muted">Title</label><input type="text" name="title" class="form-control" placeholder="e.g. Meeting Rescheduled" required></div>
<div class="mb-3"><label class="form-label small fw-bold text-muted">Message</label><textarea name="message" class="form-control" rows="3" placeholder="Write your announcement here..." required></textarea></div>
<div class="mb-3"><label class="form-label small fw-bold text-muted">Priority</label>
<select name="priority" class="form-select form-select-sm" style="max-width:200px;">
<option value="normal">Normal</option>
<option value="important">Important</option>
<option value="urgent">Urgent</option>
</select></div>
<button type="submit" name="create" class="btn btn-dark rounded-pill px-4"><i class="bi bi-send me-2"></i>Publish</button>
</form>
</div>

<h5 class="fw-bold mb-3">Previous Announcements</h5>
<?php if (empty($announcements)): ?>
<div class="glass-card p-4 text-center text-muted"><i class="bi bi-inbox fs-3 d-block mb-2 opacity-25"></i>No announcements yet.</div>
<?php else: ?>
<?php foreach ($announcements as $ann): ?>
<div class="glass-card p-3 mb-3 priority-<?= $ann['priority'] ?>">
<div class="d-flex justify-content-between align-items-start">
<div>
<h6 class="fw-bold mb-1"><?= htmlspecialchars($ann['title']) ?></h6>
<p class="small text-muted mb-2"><?= nl2br(htmlspecialchars($ann['message'])) ?></p>
<span class="text-muted" style="font-size:0.7rem;"><i class="bi bi-person me-1"></i><?= htmlspecialchars($ann['author'] ?? 'System') ?> &bull; <?= date('d M Y, H:i', strtotime($ann['created_at'])) ?>
<?php if ($ann['priority'] !== 'normal'): ?> &bull; <span class="badge bg-<?= $ann['priority'] === 'urgent' ? 'danger' : 'warning text-dark' ?> rounded-pill" style="font-size:0.65rem;"><?= ucfirst($ann['priority']) ?></span><?php endif; ?>
</span>
</div>
<a href="?delete=<?= $ann['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Delete this announcement?')"><i class="bi bi-trash3"></i></a>
</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>