<?php
require 'config.php';

// Security Check: Restricted to Chairperson, Treasury, Loan Chair, and Trustee
$allowed_roles = ['Chairperson', 'Treasury', 'Loan Chairperson', 'Trustee', 'dev'];
if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: profile.php');
    exit;
}

$current_role = $_SESSION['role'];
$member_id = $_SESSION['member_id'];
$success = '';
$error = '';

// --- 1. HANDLE UWEZO LOAN ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $loan_id = $_POST['loan_id'];
    $action = $_POST['action']; 

    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("SELECT amount, status, member_id FROM uwezo_loans WHERE id = ?");
        $stmt->execute([$loan_id]);
        $loan = $stmt->fetch();
        $applicant_id = isset($loan['member_id']) ? (int)$loan['member_id'] : 0;

        if ($action === 'approve_instant' && ($current_role === 'Loan Chairperson' || $current_role === 'dev')) {
            // Instant Approval by Loan Chair
            $stmt = $pdo->prepare("UPDATE uwezo_loans SET status = 'approved', approved_by = ?, approval_date = NOW(), balance = ? WHERE id = ?");
            $stmt->execute([$member_id, $loan['amount'], $loan_id]);
            log_admin_activity($pdo, "Uwezo Loan Approved", "Loan ID: $loan_id - Instant approval by Loan Chairperson");
            $success = "Uwezo Loan instantly approved by Loan Chairperson.";
        } 
        elseif ($action === 'vote_support') {
            // Cast a support vote
            $stmt = $pdo->prepare("INSERT IGNORE INTO loan_votes (loan_id, loan_type, voter_id, vote) VALUES (?, 'uwezo', ?, 'approve')");
            $stmt->execute([$loan_id, $member_id]);
            log_admin_activity($pdo, "Uwezo Vote Cast", "Loan ID: $loan_id - Support vote");
            $success = "Your support vote for this welfare request has been recorded.";
        }
        elseif ($action === 'rejected' && ($current_role === 'Loan Chairperson' || $current_role === 'dev')) {
            $stmt = $pdo->prepare("UPDATE uwezo_loans SET status = 'rejected', approved_by = ?, approval_date = NOW() WHERE id = ?");
            $stmt->execute([$member_id, $loan_id]);
            log_admin_activity($pdo, "Uwezo Loan Rejected", "Loan ID: $loan_id - Rejected by Loan Chairperson");
            $error = "Request rejected by Loan Chairperson.";
        }

        $pdo->commit();

        // Notify applicant (in-app + email) for approval or rejection
        if ($applicant_id && ($action === 'approve_instant' || $action === 'rejected')) {
            try {
                require_once __DIR__ . '/includes/NotificationService.php';
                if ($action === 'approve_instant') {
                    NotificationService::notify($applicant_id, "Uwezo Loan Approved", "Your Uwezo loan request has been approved by the Loan Chairperson.", 'approval');
                } else {
                    NotificationService::notify($applicant_id, "Uwezo Loan Not Approved", "Your Uwezo loan request was not approved. Please contact the group for details.", 'approval');
                }
            } catch (Exception $e) {
                error_log("admin_welfare notification: " . $e->getMessage());
            }
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "System Error: " . $e->getMessage();
    }
}

// --- 2. FETCH PENDING UWEZO LOANS WITH VOTE COUNTS ---
$stmt = $pdo->query("
    SELECT u.*, m.name, m.national_id, 
    (SELECT welfare_balance FROM member_financial_summary WHERE id = m.id) as current_welfare,
    (SELECT COUNT(*) FROM loan_votes WHERE loan_id = u.id AND loan_type = 'uwezo') as support_votes
    FROM uwezo_loans u 
    JOIN members m ON u.member_id = m.id 
    WHERE u.status = 'pending' 
    ORDER BY u.request_date ASC
");
$pending_uwezo = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- 3. FETCH TOTAL MEMBERS FOR MAJORITY CALC ---
$total_members = $pdo->query("SELECT COUNT(*) FROM members WHERE status = 'active'")->fetchColumn();
$required_votes = ceil(($total_members * 2) / 3); // Two-thirds majority

// --- 4. FETCH WELFARE POOL LIQUIDITY ---
$welfare_pool = $pdo->query("SELECT SUM(welfare_balance) FROM member_financial_summary")->fetchColumn() ?: 0;

function format_currency($amount) { return 'KSh ' . number_format($amount, 2); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welfare Vetting • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --welfare: #dc3545; --bg: #f8f9fa; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; padding-top: 30px; }
        .pool-banner { background: white; border-radius: 20px; border-left: 6px solid var(--welfare); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .request-card { background: white; border-radius: 15px; border: none; margin-bottom: 1.5rem; transition: 0.2s; }
        .vote-progress { height: 8px; border-radius: 10px; background: #eee; }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Uwezo Vetting Dashboard</h2>
            <p class="text-muted small">Welfare-backed loan approval queue</p>
        </div>
        <div class="d-flex gap-2">
            <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-pill px-4"><i class="bi bi-chevron-left me-1"></i>Console</a>
            <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-4">Profile</a>
        </div>
    </div>

    <div class="pool-banner p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <small class="text-muted text-uppercase fw-bold">Available Welfare Fund</small>
                <h1 class="fw-bold text-danger mb-0"><?= format_currency($welfare_pool) ?></h1>
            </div>
            <div class="col-md-4 text-md-end opacity-25 d-none d-md-block">
                <i class="bi bi-heart-pulse-fill fs-1 text-danger"></i>
            </div>
        </div>
    </div>

    <?php if($success): ?> <div class="alert alert-success border-0 shadow-sm mb-3"><?= $success ?></div> <?php endif; ?>
    <?php if($error): ?> <div class="alert alert-danger border-0 shadow-sm mb-3"><?= $error ?></div> <?php endif; ?>

    <h5 class="fw-bold mb-3">Pending Requests (<?= count($pending_uwezo) ?>)</h5>
    
    <?php if (empty($pending_uwezo)): ?>
        <div class="card p-5 text-center text-muted rounded-4 border-0 shadow-sm">
            <i class="bi bi-clipboard2-check fs-1 opacity-25"></i>
            <p class="mt-2">No pending uwezo requests.</p>
        </div>
    <?php else: ?>
        <?php foreach ($pending_uwezo as $u): ?>
            <?php 
                $current_welfare = (float)$u['current_welfare'];
                $requested_amount = (float)$u['amount'];
                // Qualification Rules
                $qualified = ($requested_amount == 5000 && $current_welfare >= 1000) || ($requested_amount == 10000 && $current_welfare >= 2000);
                $vote_percent = ($u['support_votes'] / $required_votes) * 100;
            ?>
            <div class="card request-card p-4 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($u['name']) ?></h6>
                        <p class="small text-muted mb-2">Member ID: #<?= $u['member_id'] ?></p>
                        <span class="badge rounded-pill <?= $qualified ? 'bg-success' : 'bg-warning text-dark' ?> py-1 px-3">
                            <?= $qualified ? 'Criteria Met' : 'Low Welfare Balance: ' . format_currency($current_welfare) ?>
                        </span>
                    </div>
                    <div class="col-md-4 text-center">
                        <small class="text-muted d-block mb-1">Requesting Assistance</small>
                        <h3 class="fw-bold text-dark mb-0"><?= format_currency($requested_amount) ?></h3>
                        
                        <div class="mt-3 px-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Support Votes</span>
                                <span><?= $u['support_votes'] ?> / <?= $required_votes ?></span>
                            </div>
                            <div class="progress vote-progress">
                                <div class="progress-bar bg-primary" style="width: <?= min(100, $vote_percent) ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <form method="POST" class="d-flex flex-column gap-2 align-items-md-end">
                            <input type="hidden" name="loan_id" value="<?= $u['id'] ?>">
                            
                            <?php if ($current_role === 'Loan Chairperson' || $current_role === 'dev'): ?>
                                <button name="action" value="approve_instant" class="btn btn-danger w-100 rounded-pill shadow-sm" <?= !$qualified ? 'disabled' : '' ?>>
                                    <i class="bi bi-lightning-fill"></i> Approve Instantly
                                </button>
                                <button name="action" value="rejected" class="btn btn-link text-danger btn-sm text-decoration-none mt-1">Decline Request</button>
                            <?php else: ?>
                                <button name="action" value="vote_support" class="btn btn-primary w-100 rounded-pill" <?= !$qualified ? 'disabled' : '' ?>>
                                    <i class="bi bi-hand-thumbs-up-fill"></i> Cast Support Vote
                                </button>
                                <p class="small text-muted mt-2 mb-0">Only the Loan Chair can approve instantly.</p>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>