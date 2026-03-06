<?php
require 'config.php';

// --- LOGOUT LOGIC ---
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_destroy();
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['member_id']) || !isset($_SESSION['paid']) || $_SESSION['paid'] !== 1) {
    header('Location: login.php');
    exit;
}

$member_id = $_SESSION['member_id'];
$success_msg = '';
$error_msg = '';

// Friendly display for redirected messages (?error=... or ?success=...)
if (isset($_GET['error']) && $_GET['error'] !== '') {
    $error_msg = $_GET['error'];
}
if (isset($_GET['success']) && $_GET['success'] !== '') {
    $success_msg = $_GET['success'];
}

// --- HANDLE PAYOUT PREFERENCE UPDATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_preference'])) {
    $pref = $_POST['payout_preference'];
    try {
        $stmt = $pdo->prepare("UPDATE members SET payout_preference = ? WHERE id = ?");
        $stmt->execute([$pref, $member_id]);
        $success_msg = "Your payout preference has been updated.";
    } catch (PDOException $e) {
        $error_msg = "Update failed: " . $e->getMessage();
    }
}

try {
    // Fetch Member Core Data
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();

    // Fetch Financial Summary (Updated with Dividend Weight)
    $stmt = $pdo->prepare("SELECT * FROM member_financial_summary WHERE id = ?");
    $stmt->execute([$member_id]);
    $financials = $stmt->fetch();

    // --- FETCH PENDING LOANS FOR VOTING ---
    $stmt = $pdo->prepare("
        SELECT l.id, l.amount, l.purpose, m.name COLLATE utf8mb4_general_ci as applicant_name, 'normal' COLLATE utf8mb4_general_ci as loan_type 
        FROM normal_loans l JOIN members m ON l.member_id = m.id
        WHERE l.status = 'pending' AND l.id NOT IN (SELECT loan_id FROM loan_votes WHERE voter_id = ? AND loan_type = 'normal')
        UNION
        SELECT l.id, l.amount, l.purpose, m.name COLLATE utf8mb4_general_ci as applicant_name, 'table_banking' COLLATE utf8mb4_general_ci as loan_type 
        FROM table_banking_loans l JOIN members m ON l.member_id = m.id
        WHERE l.status = 'pending' AND l.id NOT IN (SELECT loan_id FROM loan_votes WHERE voter_id = ? AND loan_type = 'table_banking')
        UNION
        SELECT l.id, l.amount, l.purpose, m.name COLLATE utf8mb4_general_ci as applicant_name, 'uwezo' COLLATE utf8mb4_general_ci as loan_type 
        FROM uwezo_loans l JOIN members m ON l.member_id = m.id
        WHERE l.status = 'pending' AND l.id NOT IN (SELECT loan_id FROM loan_votes WHERE voter_id = ? AND loan_type = 'uwezo')
    ");
    $stmt->execute([$member_id, $member_id, $member_id]);
    $pending_votes = $stmt->fetchAll();
    
    // --- FETCH RECENT ANNOUNCEMENTS (last 30 days) ---
    $stmt = $pdo->query("SELECT a.*, m.name as author FROM announcements a LEFT JOIN members m ON a.admin_id = m.id WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY a.created_at DESC LIMIT 10");
    $announcements = $stmt->fetchAll();

    // --- EMAIL REMINDER CHECK ---
    $needs_email = empty($member['email']);

    $notif_count = count($pending_votes) + count($announcements) + ($needs_email ? 1 : 0);

} catch (PDOException $e) {
    die("System Error: " . $e->getMessage());
}

// Helpers for UI
function format_currency($amount) { return 'KSh ' . number_format($amount, 2); }
$names = explode(" ", $member['name']);
$initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg border-bottom fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-success" href="profile.php"><i class="bi bi-house-heart me-1"></i>JERA MOYIE</a>
        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown me-3">
                <button class="btn btn-light position-relative rounded-circle p-2 shadow-sm" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <?php if ($notif_count > 0): ?>
                        <span class="position-absolute translate-middle badge rounded-pill bg-danger notif-badge">
                            <?= $notif_count ?>
                        </span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-notif p-0 overflow-hidden">
                    <div class="p-3 border-bottom bg-light">
                        <h6 class="mb-0 fw-bold">Group Notifications</h6>
                    </div>
                    <div style="max-height: 350px; overflow-y: auto;">
                        <?php if ($notif_count > 0): ?>
                            <?php foreach ($pending_votes as $pv): ?>
                                <div class="notif-item">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="badge bg-success bg-opacity-10 text-success small"><?= strtoupper(str_replace('_', ' ', $pv['loan_type'])) ?></span>
                                        <small class="text-muted">New Request</small>
                                    </div>
                                    <p class="small mb-2"><strong><?= htmlspecialchars($pv['applicant_name']) ?></strong> requested <?= format_currency($pv['amount']) ?>.</p>
                                    <form action="cast_vote.php" method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="loan_id" value="<?= $pv['id'] ?>">
                                        <input type="hidden" name="loan_type" value="<?= $pv['loan_type'] ?>">
                                        <button name="vote" value="approve" class="btn btn-xs btn-success py-1 px-3 rounded-pill small">Approve</button>
                                        <button name="vote" value="reject" class="btn btn-xs btn-outline-danger py-1 px-3 rounded-pill small">Reject</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-check2-circle fs-2"></i>
                                <p class="small mt-2 mb-0">No pending actions</p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($announcements)): ?>
                        <div class="notif-item bg-light text-center py-2"><small class="text-muted">Announcements</small></div>
                        <?php foreach ($announcements as $ann): ?>
                        <div class="notif-item">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="badge bg-<?= $ann['priority'] === 'urgent' ? 'danger' : ($ann['priority'] === 'important' ? 'warning text-dark' : 'secondary') ?> bg-opacity-75 small"><?= ucfirst($ann['priority']) ?></span>
                                <small class="text-muted"><?= date('d M', strtotime($ann['created_at'])) ?></small>
                            </div>
                            <p class="small mb-1 fw-bold"><?= htmlspecialchars($ann['title']) ?></p>
                            <p class="small mb-0 text-muted"><?= htmlspecialchars(substr($ann['message'], 0, 80)) ?><?= strlen($ann['message']) > 80 ? '...' : '' ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($needs_email): ?>
                        <div class="notif-item bg-warning bg-opacity-10">
                            <a href="edit_profile.php" class="text-decoration-none d-block">
                                <p class="small mb-0 text-danger"><i class="bi bi-envelope-exclamation me-2"></i><strong>Add your email</strong> for password recovery.</p>
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="notif-item bg-light text-center py-2"><small class="text-muted">Reminders</small></div>
                        <div class="notif-item">
                            <p class="small mb-0 text-dark"><i class="bi bi-calendar-event me-2"></i>Monday meeting starts at 4:00 PM.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dropdown">
                <button class="btn btn-outline-dark btn-sm rounded-pill px-3 dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($names[0]) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                    <li><a class="dropdown-item text-danger" href="profile.php?logout=1"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4 pt-5">
    <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4 mt-2">
        <div class="col-lg-4">
            <div class="glass-card p-4 text-center h-100">
                <div class="mx-auto rounded-circle bg-success text-white d-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px; font-size: 2rem;">
                    <?= $initials ?>
                </div>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars($member['name']) ?></h4>
                <p class="text-muted small mb-4">Member ID: #<?= str_pad($member['id'], 4, '0', STR_PAD_LEFT) ?></p>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-main py-2 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="bi bi-plus-circle me-2"></i>Make a Payment
                    </button>
                    <a href="loan_request.php" class="btn btn-outline-dark border-2 rounded-pill py-2">Request Loan</a>
                    
                    <?php if ($member['is_admin'] == 1): ?>
                        <a href="admin_dashboard.php" class="btn btn-dark w-100 py-2 rounded-pill mt-1">
                            <i class="bi bi-speedometer2 me-2"></i>Official Console
                        </a>
                    <?php endif; ?>
                    <a href="edit_profile.php" class="btn btn-outline-secondary w-100 py-2 rounded-pill mt-1">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </a>
                </div>

                <div class="mt-4 pt-4 border-top text-start">
                    <h6 class="fw-bold small mb-3 text-muted text-uppercase opactiy-75">Share-Out Preference</h6>
                    <form method="POST">
                        <select name="payout_preference" class="form-select form-select-sm border-2 mb-2 rounded-3">
                            <option value="savings" <?= ($member['payout_preference'] == 'savings') ? 'selected' : '' ?>>Re-invest (Savings)</option>
                            <option value="mpesa" <?= ($member['payout_preference'] == 'mpesa') ? 'selected' : '' ?>>Direct M-PESA</option>
                            <option value="cash" <?= ($member['payout_preference'] == 'cash') ? 'selected' : '' ?>>Physical Cash</option>
                        </select>
                        <button type="submit" name="update_preference" class="btn btn-sm btn-outline-dark w-100 rounded-pill mt-1">Update Preference</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <h3 class="fw-bold mb-4 section-title">Financial Overview</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="glass-card p-4 stat-card h-100">
                        <small class="text-muted text-uppercase fw-bold opacity-75">Combined Savings Base</small>
                        <h2 class="fw-bold text-success mt-2"><?= format_currency($financials['total_dividend_weight']) ?></h2>
                        <div class="d-flex justify-content-between mt-3 small text-muted">
                            <span>Normal: <?= format_currency($financials['normal_savings_balance']) ?></span>
                            <span>Weekly: <?= format_currency($financials['total_weekly_paid']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="glass-card p-4 stat-card h-100 border-start border-danger border-4">
                        <small class="text-muted text-uppercase fw-bold opacity-75">Active Loan Balance</small>
                        <h2 class="fw-bold text-danger mt-2"><?= format_currency($financials['normal_loans_balance'] + $financials['uwezo_loans_balance'] + $financials['table_banking_loans_balance']) ?></h2>
                        <a href="loan_statement.php" class="small text-decoration-none text-danger fw-bold">View Full Statement <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h5 class="fw-bold mb-3 section-title h6 text-uppercase opacity-75">Group Ledgers</h5>
                <div class="row g-3 text-center">
                    <div class="col-4">
                        <a href="normal_book.php" class="glass-card p-3 d-block text-decoration-none text-dark shadow-sm">
                            <div class="icon-box mx-auto mb-2 bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-journal-text fs-4"></i>
                            </div>
                            <small class="fw-bold">Normal</small>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="table_banking_book.php" class="glass-card p-3 d-block text-decoration-none text-dark shadow-sm">
                            <div class="icon-box mx-auto mb-2 bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-bank fs-4"></i>
                            </div>
                            <small class="fw-bold">Table</small>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="welfare_book.php" class="glass-card p-3 d-block text-decoration-none text-dark shadow-sm">
                            <div class="icon-box mx-auto mb-2 bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-heart-fill fs-4"></i>
                            </div>
                            <small class="fw-bold">Welfare</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold section-title">Payment Portal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_payment.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase opacity-75">Category</label>
                        <select name="type" class="form-select py-2 border-2 rounded-3" required>
                            <option value="weekly">Weekly Contribution (KSh 3,000)</option>
                            <option value="normal_savings">Normal Savings Deposit</option>
                            <option value="table_banking">Table Banking Shares</option>
                            <option value="welfare">Welfare Contribution</option>
                            <option value="loan_repayment">Loan Repayment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase opacity-75">Amount (KSh)</label>
                        <input type="number" name="amount" class="form-control py-2 border-2 rounded-3" placeholder="3000" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase opacity-75">M-PESA Phone Number</label>
                        <input type="text" name="phone" class="form-control py-2 border-2 rounded-3" value="<?= htmlspecialchars($member['phone']) ?>" required>
                    </div>
                    <div class="d-grid pt-2">
                        <button type="submit" class="btn btn-main py-3 rounded-pill shadow">Initiate M-Pesa Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
