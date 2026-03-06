<?php
require 'config.php';
$allowed = ['Chairperson', 'Assistant Chairperson', 'Treasury', 'dev'];
if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], $allowed)) {
    header('Location: profile.php'); exit;
}
$filter_action = $_GET['action'] ?? '';
$filter_search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 50;
$offset = ($page - 1) * $per_page;
$where = [];
$params = [];
if ($filter_action) { $where[] = "al.action = ?"; $params[] = $filter_action; }
if ($filter_search) { $where[] = "(al.details LIKE ? OR m.name LIKE ?)"; $params[] = "%$filter_search%"; $params[] = "%$filter_search%"; }
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_logs al LEFT JOIN members m ON al.member_id = m.id $where_sql");
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = max(1, ceil($total / $per_page));
$stmt = $pdo->prepare("SELECT al.*, m.name as admin_name FROM admin_logs al LEFT JOIN members m ON al.member_id = m.id $where_sql ORDER BY al.created_at DESC LIMIT $per_page OFFSET $offset");
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$actions = $pdo->query("SELECT DISTINCT action FROM admin_logs ORDER BY action")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Audit Trail - Jera Moyie</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root { --primary: #1a5928; --bg: #f3f4f6; }
body { background: var(--bg); font-family: 'Inter', sans-serif; padding-top: 30px; }
.audit-card { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.log-row:hover { background: #f8f9fa; }
.action-badge { font-size: 0.72rem; padding: 4px 10px; border-radius: 50px; font-weight: 600; }
.badge-approve { background: #dcfce7; color: #166534; }
.badge-reject { background: #fee2e2; color: #991b1b; }
.badge-edit { background: #dbeafe; color: #1e40af; }
.badge-delete { background: #fce4ec; color: #b71c1c; }
.badge-finance { background: #fef3c7; color: #92400e; }
.badge-default { background: #e5e7eb; color: #374151; }
.ip-text { font-family: monospace; font-size: 0.75rem; color: #9ca3af; }
</style>
</head>
<body>
<div class="container mb-5">
<div class="d-flex justify-content-between align-items-center mb-4">
<div><h2 class="fw-bold mb-0"><i class="bi bi-shield-check me-2"></i>Audit Trail</h2><p class="text-muted small">All admin activities are recorded here for accountability</p></div>
<div class="d-flex gap-2">
    <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-pill px-4 btn-sm"><i class="bi bi-chevron-left me-1"></i>Console</a>
    <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">Profile</a>
</div>
</div>
<div class="audit-card p-3 mb-4">
<form method="GET" class="row g-2 align-items-end">
<div class="col-md-4"><label class="small fw-bold text-muted">Search Details</label><input type="text" name="search" class="form-control form-control-sm" placeholder="Name, ID, details..." value="<?= htmlspecialchars($filter_search) ?>"></div>
<div class="col-md-3"><label class="small fw-bold text-muted">Action Type</label><select name="action" class="form-select form-select-sm"><option value="">All Actions</option><?php foreach ($actions as $a): ?><option value="<?= htmlspecialchars($a) ?>" <?= $filter_action === $a ? 'selected' : '' ?>><?= htmlspecialchars($a) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><button class="btn btn-sm btn-dark rounded-pill w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
<?php if ($filter_action || $filter_search): ?><div class="col-md-2"><a href="admin_audit_log.php" class="btn btn-sm btn-outline-secondary rounded-pill w-100">Clear</a></div><?php endif; ?>
</form>
</div>
<div class="d-flex gap-3 mb-3">
<span class="badge bg-dark rounded-pill px-3 py-2"><?= number_format($total) ?> total records</span>
<span class="text-muted small align-self-center">Page <?= $page ?> of <?= $total_pages ?></span>
</div>
<div class="audit-card">
<div class="table-responsive">
<table class="table table-sm align-middle mb-0">
<thead><tr class="small text-muted text-uppercase" style="font-size:0.72rem; letter-spacing:0.5px;"><th class="ps-3">Time</th><th>Admin</th><th>Action</th><th>Details</th><th>IP</th></tr></thead>
<tbody>
<?php if (empty($logs)): ?>
<tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2 opacity-25"></i>No audit records found.</td></tr>
<?php else: ?>
<?php foreach ($logs as $log):
$action_lower = strtolower($log['action']);
$badge = 'badge-default';
if (str_contains($action_lower, 'approv')) $badge = 'badge-approve';
elseif (str_contains($action_lower, 'reject')) $badge = 'badge-reject';
elseif (str_contains($action_lower, 'edit') || str_contains($action_lower, 'update')) $badge = 'badge-edit';
elseif (str_contains($action_lower, 'delet')) $badge = 'badge-delete';
elseif (str_contains($action_lower, 'payout') || str_contains($action_lower, 'financial') || str_contains($action_lower, 'repayment')) $badge = 'badge-finance';
?>
<tr class="log-row">
<td class="ps-3 small text-muted" style="white-space:nowrap;"><?= date('d M Y, H:i', strtotime($log['created_at'])) ?></td>
<td class="fw-bold small"><?= htmlspecialchars($log['admin_name'] ?? 'System') ?></td>
<td><span class="action-badge <?= $badge ?>"><?= htmlspecialchars($log['action']) ?></span></td>
<td class="small text-muted" style="max-width:350px;"><?= htmlspecialchars($log['details'] ?? '') ?></td>
<td class="ip-text"><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table></div></div>
<?php if ($total_pages > 1): ?>
<nav class="mt-3"><ul class="pagination pagination-sm justify-content-center">
<li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page - 1 ?>&action=<?= urlencode($filter_action) ?>&search=<?= urlencode($filter_search) ?>">Prev</a></li>
<?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
<li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&action=<?= urlencode($filter_action) ?>&search=<?= urlencode($filter_search) ?>"><?= $i ?></a></li>
<?php endfor; ?>
<li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page + 1 ?>&action=<?= urlencode($filter_action) ?>&search=<?= urlencode($filter_search) ?>">Next</a></li>
</ul></nav>
<?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>