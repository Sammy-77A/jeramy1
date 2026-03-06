<?php
require 'config.php';

// Security: Must be an admin/office bearer
if (!isset($_SESSION['member_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: profile.php');
    exit;
}

$admin_id = $_SESSION['member_id'];
$admin_role = $_SESSION['role']; 

// 1. Calculate the 2/3 Threshold dynamically
$stmt = $pdo->query("SELECT COUNT(*) FROM members WHERE is_native = 1 AND paid = 1");
$total_members = $stmt->fetchColumn();
$threshold = ceil(($total_members * 2) / 3); 

$success = '';
$error = '';

// 2. Process POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loan_id = $_POST['loan_id'];
    $type = $_POST['loan_type'];
    $table = $type . "_loans";

    // PATH A: Loan Chairperson Instant Approval
    if (isset($_POST['instant_approve']) && $admin_role === 'Loan Chairperson') {
        $stmt = $pdo->prepare("UPDATE $table SET status = 'approved', approved_by = ?, approval_date = NOW() WHERE id = ?");
        $stmt->execute([$admin_id, $loan_id]);
        log_admin_activity($pdo, "Loan Instant Approval", "Loan ID: $loan_id, Type: $type - Approved by Chairperson");
        $success = "Loan successfully approved by Chairperson.";
        try {
            $stmt = $pdo->prepare("SELECT member_id FROM $table WHERE id = ?");
            $stmt->execute([$loan_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['member_id'])) {
                require_once __DIR__ . '/includes/NotificationService.php';
                $typeLabel = ucfirst(str_replace('_', ' ', $type));
                NotificationService::notify((int)$row['member_id'], "Loan Approved", "Your $typeLabel loan has been approved by the Loan Chairperson.", 'approval');
            }
        } catch (Exception $e) {
            error_log("admin_loans instant_approve notification: " . $e->getMessage());
        }
    }

    // PATH B: Admin Democratic Vote
    if (isset($_POST['cast_vote'])) {
        $vote = $_POST['cast_vote']; 

        try {
            // Record the vote
            $stmt = $pdo->prepare("INSERT INTO loan_votes (loan_id, loan_type, voter_id, vote) VALUES (?, ?, ?, ?)");
            $stmt->execute([$loan_id, $type, $admin_id, $vote]);
            log_admin_activity($pdo, "Loan Vote Cast", "Loan ID: $loan_id, Type: $type, Vote: $vote");

            // Check progress toward threshold
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM loan_votes WHERE loan_id = ? AND loan_type = ? AND vote = 'approve'");
            $stmt->execute([$loan_id, $type]);
            $current_votes = $stmt->fetchColumn();

            if ($current_votes >= $threshold) {
                $pdo->prepare("UPDATE $table SET status = 'approved', approval_date = NOW() WHERE id = ?")->execute([$loan_id]);
                $success = "Threshold reached! Loan automatically approved via 2/3 majority.";
                try {
                    $stmt = $pdo->prepare("SELECT member_id FROM $table WHERE id = ?");
                    $stmt->execute([$loan_id]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row && !empty($row['member_id'])) {
                        require_once __DIR__ . '/includes/NotificationService.php';
                        $typeLabel = ucfirst(str_replace('_', ' ', $type));
                        NotificationService::notify((int)$row['member_id'], "Loan Approved", "Your $typeLabel loan request has been approved by the group (2/3 majority).", 'approval');
                    }
                } catch (Exception $e) {
                    error_log("admin_loans cast_vote notification: " . $e->getMessage());
                }
            } else {
                $success = "Vote recorded. Progress: $current_votes / $threshold approved.";
            }
        } catch (PDOException $e) {
            $error = "Error: You have already voted on this loan.";
        }
    }
}

// 3. Fetch Pending Loans WITH Applicant Names
$pending = [];
foreach (['normal', 'table_banking', 'uwezo'] as $t) {
    // JOINing with members table to get the name of the person who applied
    $stmt = $pdo->query("
        SELECT l.*, m.name as applicant_name, '$t' as loan_type 
        FROM {$t}_loans l
        JOIN members m ON l.member_id = m.id
        WHERE l.status = 'pending'
    ");
    $pending = array_merge($pending, $stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Approvals • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #1a5928; --bg: #f3f4f6; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; padding-top: 40px; }
        .approval-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn-approve { background: var(--primary); color: white; border: none; }
        .progress { height: 8px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Admin Loan Console</h2>
        <div class="d-flex gap-2">
            <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-pill shadow-sm"><i class="bi bi-chevron-left me-1"></i>Console</a>
            <a href="profile.php" class="btn btn-outline-secondary rounded-pill shadow-sm">Profile</a>
        </div>
    </div>

    <?php if($success): ?> <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3"><?= $success ?></div> <?php endif; ?>
    <?php if($error): ?> <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3"><?= $error ?></div> <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($pending as $loan): 
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM loan_votes WHERE loan_id = ? AND loan_type = ? AND vote = 'approve'");
            $stmt->execute([$loan['id'], $loan['loan_type']]);
            $count = $stmt->fetchColumn();
            $progress = ($threshold > 0) ? ($count / $threshold) * 100 : 0;
        ?>
        <div class="col-md-6">
            <div class="card approval-card p-4">
                <span class="badge bg-success bg-opacity-10 text-success mb-2"><?= strtoupper(str_replace('_', ' ', $loan['loan_type'])) ?></span>
                <h3 class="fw-bold">KSh <?= number_format($loan['amount'], 2) ?></h3>
                
                <p class="mb-1 text-dark"><strong>Applicant:</strong> <?= htmlspecialchars($loan['applicant_name']) ?></p>
                <p class="text-muted small">Purpose: <?= htmlspecialchars($loan['purpose']) ?></p>

                <div class="mb-4">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted fw-bold">Democratic Progress</span>
                        <span class="fw-bold"><?= $count ?> / <?= $threshold ?></span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: <?= $progress ?>%"></div>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                    <input type="hidden" name="loan_type" value="<?= $loan['loan_type'] ?>">
                    
                    <div class="row g-2">
                        <?php if ($admin_role === 'Loan Chairperson'): ?>
                        <div class="col-12 mb-2">
                            <button type="submit" name="instant_approve" class="btn btn-approve w-100 py-3 fw-bold rounded-3">
                                <i class="bi bi-shield-check me-2"></i>Chairperson Instant Approval
                            </button>
                        </div>
                        <?php endif; ?>

                        <div class="col-6">
                            <button type="submit" name="cast_vote" value="approve" class="btn btn-primary w-100 py-2 rounded-3">
                                <i class="bi bi-hand-thumbs-up me-1"></i> Approve
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="cast_vote" value="reject" class="btn btn-danger w-100 py-2 rounded-3">
                                <i class="bi bi-hand-thumbs-down me-1"></i> Reject
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>