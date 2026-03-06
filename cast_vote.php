<?php
require 'config.php';

if (!isset($_SESSION['member_id']) || !isset($_POST['vote'])) {
    header("Location: profile.php");
    exit;
}

$voter_id = $_SESSION['member_id'];
$loan_id = $_POST['loan_id'];
$type = $_POST['loan_type'];
$vote = $_POST['vote'];

try {
    // 1. Record the vote
    $stmt = $pdo->prepare("INSERT INTO loan_votes (loan_id, loan_type, voter_id, vote) VALUES (?, ?, ?, ?)");
    $stmt->execute([$loan_id, $type, $voter_id, $vote]);

    // 2. Check if the 2/3 threshold is met
    // Total active native members
    $stmt = $pdo->query("SELECT COUNT(*) FROM members WHERE is_native = 1 AND paid = 1");
    $total_members = $stmt->fetchColumn();
    $threshold = ceil(($total_members * 2) / 3);

    // Current approve votes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM loan_votes WHERE loan_id = ? AND loan_type = ? AND vote = 'approve'");
    $stmt->execute([$loan_id, $type]);
    $current_votes = $stmt->fetchColumn();

    // 3. Update Loan status if threshold met
    if ($current_votes >= $threshold) {
        $table = $type . "_loans";
        $stmt = $pdo->prepare("UPDATE $table SET status = 'approved', approval_date = NOW() WHERE id = ?");
        $stmt->execute([$loan_id]);
        // Notify applicant (in-app + email)
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
            error_log("cast_vote notification: " . $e->getMessage());
        }
    }

    log_admin_activity($pdo, "Vote Cast", "Loan ID: $loan_id, Type: $type, Vote: $vote");
    header("Location: profile.php?success=" . urlencode("Vote recorded successfully."));
}
catch (PDOException $e) {
    header("Location: profile.php?error=" . urlencode("You have already voted on this request."));
}
exit;