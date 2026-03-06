<?php
require 'config.php';
$allowed_roles = ['Chairperson', 'Assistant Chairperson', 'Secretary', 'Assistant Secretary', 'Treasury', 'Trustee', 'Loan Chairperson', 'dev'];
if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], $allowed_roles)) { header('Location: profile.php'); exit; }
$member_id = intval($_GET['id'] ?? 0);
if ($member_id <= 0) { header('Location: admin_members.php'); exit; }
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?"); $stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$member) { header('Location: admin_members.php'); exit; }
$displayPhone = preg_replace('/^254/', '0', $member['phone']);
$stmt = $pdo->prepare("SELECT * FROM member_financial_summary WHERE id = ?"); $stmt->execute([$member_id]);
$finance = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM normal_savings WHERE member_id = ? ORDER BY transaction_date DESC LIMIT 50"); $stmt->execute([$member_id]);
$normal_savings = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM table_banking_shares WHERE member_id = ? ORDER BY transaction_date DESC LIMIT 50"); $stmt->execute([$member_id]);
$table_shares = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM welfare_contributions WHERE member_id = ? ORDER BY contribution_date DESC LIMIT 50"); $stmt->execute([$member_id]);
$welfare = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM weekly_contributions WHERE member_id = ? ORDER BY contribution_date DESC LIMIT 50"); $stmt->execute([$member_id]);
$weekly = $stmt->fetchAll(PDO::FETCH_ASSOC);
$loans = []; try { $stmt = $pdo->prepare("SELECT * FROM loans WHERE member_id = ? ORDER BY request_date DESC LIMIT 50"); $stmt->execute([$member_id]); $loans = $stmt->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) {}
$repayments = []; try { $stmt = $pdo->prepare("SELECT * FROM loan_repayments WHERE member_id = ? ORDER BY repayment_date DESC LIMIT 50"); $stmt->execute([$member_id]); $repayments = $stmt->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) {}
$attendance = []; try { $stmt = $pdo->prepare("SELECT * FROM attendance WHERE member_id = ? ORDER BY meeting_date DESC LIMIT 50"); $stmt->execute([$member_id]); $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) {}
function fmt($a) { return 'KSh ' . number_format((float)$a, 2); }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($member['name']) ?> - Member Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--primary:#1a5928;--primary-light:rgba(26,89,40,.08);--bg:#f3f5f7;--accent:#f59e0b}
body{background:var(--bg);font-family:'Inter',sans-serif;padding-top:20px;padding-bottom:50px}
.profile-header{background:linear-gradient(135deg,var(--primary),#2d8a3e);border-radius:20px;color:#fff;padding:2rem;margin-bottom:1.5rem;position:relative;overflow:hidden}
.profile-header::before{content:'';position:absolute;top:-50%;right:-20%;width:300px;height:300px;background:rgba(255,255,255,.05);border-radius:50%}
.avatar-circle{width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:2rem;border:3px solid rgba(255,255,255,.3)}
.info-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.04);border:1px solid #f0f0f0;margin-bottom:1rem}
.info-card .card-header{background:var(--primary-light);border-bottom:1px solid #e8f5e9;padding:1rem 1.25rem;border-radius:16px 16px 0 0;font-weight:600;color:var(--primary);display:flex;align-items:center;gap:10px;cursor:pointer}
.info-card .card-body{padding:1.25rem}
.stat-pill{background:#fff;border-radius:14px;padding:1rem 1.25rem;box-shadow:0 2px 10px rgba(0,0,0,.04);border:1px solid #f0f0f0;text-align:center}
.stat-pill .label{font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;font-weight:600}
.stat-pill .val{font-size:1.1rem;font-weight:700;margin-top:4px}
.badge-role{background:rgba(255,255,255,.2);padding:4px 14px;border-radius:50px;font-size:.8rem;font-weight:500}
.badge-status{padding:4px 14px;border-radius:50px;font-size:.75rem;font-weight:600}
.table{font-size:.85rem}.table thead{background:var(--primary);color:#fff}
.table thead th{font-weight:500;font-size:.8rem;text-transform:uppercase;letter-spacing:.3px;border:none}
.empty-state{text-align:center;padding:2rem;color:#9ca3af}
.empty-state i{font-size:2.5rem;margin-bottom:.5rem;display:block;opacity:.4}
.debt-red{color:#dc3545;font-weight:700}
.back-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 20px;border-radius:50px;text-decoration:none;font-weight:500;font-size:.9rem;transition:all .3s}
@media(max-width:768px){.profile-header{padding:1.5rem}.avatar-circle{width:60px;height:60px;font-size:1.5rem}}
</style></head><body>
<div class="container" style="max-width:1000px">
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
<a href="admin_members.php" class="back-btn btn btn-outline-dark"><i class="bi bi-arrow-left"></i> Back to Members</a>
<div class="d-flex gap-2 flex-wrap">
<a href="admin_members.php" class="back-btn btn btn-outline-secondary"><i class="bi bi-people"></i> Register</a>
<a href="admin_finance.php" class="back-btn btn btn-outline-success"><i class="bi bi-currency-exchange"></i> Finance</a>
<a href="admin_dashboard.php" class="back-btn btn btn-outline-dark"><i class="bi bi-grid"></i> Dashboard</a>
</div></div>
<div class="profile-header">
<div class="d-flex align-items-center gap-3 position-relative" style="z-index:1">
<div class="avatar-circle"><i class="bi bi-person-fill"></i></div>
<div><h2 class="fw-bold mb-1" style="font-size:1.5rem"><?= htmlspecialchars($member['name']) ?></h2>
<div class="d-flex flex-wrap gap-2 align-items-center">
<span class="badge-role"><?= htmlspecialchars($member['role']) ?></span>
<span class="badge-status <?= $member['status']==='active'?'bg-success':'bg-warning text-dark' ?>"><?= ucfirst($member['status']) ?></span>
<span class="badge-role"><?= $member['is_native']?'Native':'Community' ?></span>
</div></div></div></div>
<div class="row g-3 mb-3">
<div class="col-md-3 col-6"><div class="stat-pill"><div class="label">Member ID</div><div class="val text-dark">#<?= str_pad($member['id'],3,'0',STR_PAD_LEFT) ?></div></div></div>
<div class="col-md-3 col-6"><div class="stat-pill"><div class="label">Phone</div><div class="val text-dark"><?= $displayPhone ?></div></div></div>
<div class="col-md-3 col-6"><div class="stat-pill"><div class="label">National ID</div><div class="val text-dark"><?= htmlspecialchars($member['national_id']) ?></div></div></div>
<div class="col-md-3 col-6"><div class="stat-pill"><div class="label">Joined</div><div class="val text-dark"><?= isset($member['joined_date'])?date('d M Y',strtotime($member['joined_date'])):'N/A' ?></div></div></div>
</div>
<?php if($finance): ?>
<div class="info-card"><div class="card-header"><i class="bi bi-bar-chart-fill"></i> Financial Summary</div>
<div class="card-body"><div class="row g-3">
<div class="col-md-3 col-6"><div class="stat-pill border-start border-3 border-success"><div class="label">Normal Savings</div><div class="val text-success"><?= fmt($finance['normal_savings_balance']) ?></div></div></div>
<div class="col-md-3 col-6"><div class="stat-pill border-start border-3 border-info"><div class="label">Table Banking</div><div class="val text-info"><?= fmt($finance['table_banking_balance']) ?></div></div></div>
<div class="col-md-3 col-6"><div class="stat-pill border-start border-3 border-primary"><div class="label">Welfare Fund</div><div class="val text-primary"><?= fmt($finance['welfare_balance']) ?></div></div></div>
<div class="col-md-3 col-6"><div class="stat-pill border-start border-3 border-warning"><div class="label">Weekly Paid</div><div class="val text-warning"><?= fmt($finance['total_weekly_paid']) ?></div></div></div>
</div>
<?php $td=($finance['normal_loans_balance']??0)+($finance['table_banking_loans_balance']??0)+($finance['uwezo_loans_balance']??0); ?>
<div class="row g-3 mt-1">
<div class="col-md-3 col-6"><div class="stat-pill"><div class="label">Normal Loan</div><div class="val debt-red"><?= fmt($finance['normal_loans_balance']??0) ?></div></div></div>
<div class="col-md-3 col-6"><div class="stat-pill"><div class="label">Table Loan</div><div class="val debt-red"><?= fmt($finance['table_banking_loans_balance']??0) ?></div></div></div>
<div class="col-md-3 col-6"><div class="stat-pill"><div class="label">Uwezo Loan</div><div class="val debt-red"><?= fmt($finance['uwezo_loans_balance']??0) ?></div></div></div>
<div class="col-md-3 col-6"><div class="stat-pill border-start border-3 border-danger"><div class="label">Total Debt</div><div class="val debt-red"><?= fmt($td) ?></div></div></div>
</div></div></div>
<?php endif; ?>

<div class="info-card"><div class="card-header" data-bs-toggle="collapse" data-bs-target="#sav"><i class="bi bi-piggy-bank-fill"></i> Normal Savings <span class="badge bg-secondary ms-auto"><?= count($normal_savings) ?></span></div>
<div class="collapse show" id="sav"><div class="card-body p-0">
<?php if(empty($normal_savings)): ?><div class="empty-state"><i class="bi bi-inbox"></i><p class="small">No records</p></div>
<?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Balance</th><th>Description</th></tr></thead><tbody>
<?php foreach($normal_savings as $r): ?><tr>
<td><?= date('d M Y',strtotime($r['transaction_date'])) ?></td>
<td><span class="badge <?= $r['transaction_type']==='deposit'?'bg-success':'bg-warning text-dark' ?>"><?= ucfirst($r['transaction_type']) ?></span></td>
<td class="fw-bold"><?= fmt($r['amount']) ?></td><td><?= fmt($r['balance_after']) ?></td>
<td class="small text-muted"><?= htmlspecialchars($r['description']??'') ?></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?></div></div></div>

<div class="info-card"><div class="card-header" data-bs-toggle="collapse" data-bs-target="#tbs"><i class="bi bi-bank2"></i> Table Banking <span class="badge bg-secondary ms-auto"><?= count($table_shares) ?></span></div>
<div class="collapse" id="tbs"><div class="card-body p-0">
<?php if(empty($table_shares)): ?><div class="empty-state"><i class="bi bi-inbox"></i><p class="small">No records</p></div>
<?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Balance</th><th>Description</th></tr></thead><tbody>
<?php foreach($table_shares as $r): ?><tr>
<td><?= date('d M Y',strtotime($r['transaction_date'])) ?></td>
<td><span class="badge bg-info"><?= ucfirst($r['transaction_type']) ?></span></td>
<td class="fw-bold"><?= fmt($r['amount']) ?></td><td><?= fmt($r['balance_after']) ?></td>
<td class="small text-muted"><?= htmlspecialchars($r['description']??'') ?></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?></div></div></div>

<div class="info-card"><div class="card-header" data-bs-toggle="collapse" data-bs-target="#wlf"><i class="bi bi-heart-pulse-fill"></i> Welfare <span class="badge bg-secondary ms-auto"><?= count($welfare) ?></span></div>
<div class="collapse" id="wlf"><div class="card-body p-0">
<?php if(empty($welfare)): ?><div class="empty-state"><i class="bi bi-inbox"></i><p class="small">No records</p></div>
<?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>Date</th><th>Amount</th><th>Description</th></tr></thead><tbody>
<?php foreach($welfare as $r): ?><tr>
<td><?= date('d M Y',strtotime($r['contribution_date'])) ?></td>
<td class="fw-bold"><?= fmt($r['amount']) ?></td>
<td class="small text-muted"><?= htmlspecialchars($r['description']??'') ?></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?></div></div></div>

<div class="info-card"><div class="card-header" data-bs-toggle="collapse" data-bs-target="#wkl"><i class="bi bi-calendar-week-fill"></i> Weekly Contributions <span class="badge bg-secondary ms-auto"><?= count($weekly) ?></span></div>
<div class="collapse" id="wkl"><div class="card-body p-0">
<?php if(empty($weekly)): ?><div class="empty-state"><i class="bi bi-inbox"></i><p class="small">No records</p></div>
<?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>Date</th><th>Week</th><th>Amount</th><th>Status</th></tr></thead><tbody>
<?php foreach($weekly as $r): ?><tr>
<td><?= date('d M Y',strtotime($r['contribution_date'])) ?></td>
<td>W<?= $r['week_number'] ?>/<?= $r['year'] ?></td>
<td class="fw-bold"><?= fmt($r['amount']) ?></td>
<td><span class="badge <?= ($r['status']??'')==='paid'?'bg-success':'bg-warning text-dark' ?>"><?= ucfirst($r['status']??'Unknown') ?></span></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?></div></div></div>

<div class="info-card"><div class="card-header" data-bs-toggle="collapse" data-bs-target="#lns"><i class="bi bi-cash-stack"></i> Loans <span class="badge bg-secondary ms-auto"><?= count($loans) ?></span></div>
<div class="collapse" id="lns"><div class="card-body p-0">
<?php if(empty($loans)): ?><div class="empty-state"><i class="bi bi-inbox"></i><p class="small">No records</p></div>
<?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Status</th><th>Repaid</th></tr></thead><tbody>
<?php foreach($loans as $r): ?><tr>
<td><?= date('d M Y',strtotime($r['request_date'])) ?></td>
<td><span class="badge bg-dark"><?= htmlspecialchars($r['loan_type']??'Normal') ?></span></td>
<td class="fw-bold"><?= fmt($r['amount']) ?></td>
<td><span class="badge <?= ($r['status']??'')==='approved'?'bg-success':(($r['status']??'')==='rejected'?'bg-danger':'bg-warning text-dark') ?>"><?= ucfirst($r['status']??'') ?></span></td>
<td><?= fmt($r['total_repaid']??0) ?></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?></div></div></div>

<div class="info-card"><div class="card-header" data-bs-toggle="collapse" data-bs-target="#rpy"><i class="bi bi-arrow-repeat"></i> Repayments <span class="badge bg-secondary ms-auto"><?= count($repayments) ?></span></div>
<div class="collapse" id="rpy"><div class="card-body p-0">
<?php if(empty($repayments)): ?><div class="empty-state"><i class="bi bi-inbox"></i><p class="small">No records</p></div>
<?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>Date</th><th>Loan Type</th><th>Amount</th><th>Reference</th><th>Notes</th></tr></thead><tbody>
<?php foreach($repayments as $r): ?><tr>
<td><?= date('d M Y',strtotime($r['repayment_date'])) ?></td>
<td><span class="badge bg-secondary"><?= htmlspecialchars($r['loan_type']??'') ?></span></td>
<td class="fw-bold text-success"><?= fmt($r['amount']) ?></td>
<td class="small"><?= htmlspecialchars($r['reference_number']??'') ?></td>
<td class="small text-muted"><?= htmlspecialchars($r['notes']??'') ?></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?></div></div></div>

<div class="info-card"><div class="card-header" data-bs-toggle="collapse" data-bs-target="#att"><i class="bi bi-calendar-check-fill"></i> Attendance <span class="badge bg-secondary ms-auto"><?= count($attendance) ?></span></div>
<div class="collapse" id="att"><div class="card-body p-0">
<?php if(empty($attendance)): ?><div class="empty-state"><i class="bi bi-inbox"></i><p class="small">No records</p></div>
<?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>Meeting Date</th><th>Status</th></tr></thead><tbody>
<?php foreach($attendance as $r): ?><tr>
<td><?= date('d M Y',strtotime($r['meeting_date'])) ?></td>
<td><span class="badge <?= ($r['status']??'')==='present'?'bg-success':'bg-danger' ?>"><?= ucfirst($r['status']??'Unknown') ?></span></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?></div></div></div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
