<?php
require 'config.php';
require_once 'dompdf/autoload.inc.php'; 
use Dompdf\Dompdf;

if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], ['Treasury', 'dev'])) {
    header('Location: profile.php'); exit;
}

// 1. Recalculate Table Profit Logic
$table_interest = $pdo->query("SELECT SUM(interest_accrued) FROM table_banking_loans WHERE status = 'active'")->fetchColumn() ?: 1000.00;
$total_shares = $pdo->query("SELECT SUM(table_banking_balance) FROM member_financial_summary")->fetchColumn() ?: 1;
$rate = $table_interest / $total_shares;

// 2. Manual Payout Handler
if (isset($_POST['execute_table'])) {
    $m_id = $_POST['m_id']; $amt = $_POST['amt']; $method = $_POST['method']; $userInputCode = trim($_POST['manual_ref_code'] ?? '');

    try {
        if ($amt <= 0) throw new Exception("Amount must be greater than zero.");
        $pdo->beginTransaction();
        $check = $pdo->prepare("SELECT id FROM share_out_history WHERE member_id = ? AND cycle_type = 'quarterly' AND share_out_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)");
        $check->execute([$m_id]);
        if ($check->fetch()) throw new Exception("Error: Already paid for this quarter.");

        $final_ref = "";
        if ($method === 'mpesa') {
            if (!preg_match('/^[A-Z0-9]{10}$/', strtoupper($userInputCode))) throw new Exception("Invalid M-Pesa Code.");
            $final_ref = strtoupper($userInputCode);
        } elseif ($method === 'cash') {
            $final_ref = "T-CSH-" . date('Ymd') . "-" . strtoupper(substr(uniqid(), -4));
        } else {
            $final_ref = "T-REINV-" . date('Ymd') . "-" . $m_id;
            $pdo->prepare("INSERT INTO table_banking_shares (member_id, amount, transaction_type, transaction_date) VALUES (?, ?, 'share', CURDATE())")->execute([$m_id, $amt]);
        }

        $stmt = $pdo->prepare("INSERT INTO share_out_history (member_id, cycle_type, amount_paid, payout_method, payout_status, transaction_reference, processed_by) VALUES (?, 'quarterly', ?, ?, 'completed', ?, ?)");
        $stmt->execute([$m_id, $amt, $method, $final_ref, $_SESSION['member_id']]);

        $pdo->commit();
        log_admin_activity($pdo, "Quarterly Table Payout", "Distributed KSh $amt to Member ID $m_id. Ref: $final_ref");

        $_SESSION['success_msg'] = "Table payout recorded! Ref: $final_ref";
        header("Location: sharing_table_process.php");
        exit;
    } catch (Exception $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $_SESSION['error_msg'] = $e->getMessage(); header("Location: sharing_table_process.php"); exit; }
}

// PDF Summary
if (isset($_GET['download'])) {
    $dompdf = new Dompdf();
    $html = '<h2 style="text-align:center;">Quarterly Distribution Summary</h2><table border="1" width="100%" style="border-collapse: collapse;"><thead><tr><th>Member</th><th>Amount</th><th>Ref</th></tr></thead><tbody>';
    $history = $pdo->query("SELECT h.*, m.name FROM share_out_history h JOIN members m ON h.member_id = m.id WHERE cycle_type='quarterly' AND share_out_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)")->fetchAll();
    foreach($history as $row) { $html .= "<tr><td>".htmlspecialchars($row['name'])."</td><td>KSh ".number_format($row['amount_paid'], 2)."</td><td>{$row['transaction_reference']}</td></tr>"; }
    $html .= "</tbody></table>";
    $dompdf->loadHtml($html); $dompdf->render();
    $dompdf->stream("Quarterly_Table_ShareOut.pdf"); exit;
}

$distribution_list = $pdo->query("SELECT m.id, m.name, m.payout_preference, f.table_banking_balance, (SELECT id FROM share_out_history WHERE member_id = m.id AND cycle_type = 'quarterly' AND share_out_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)) as paid_id FROM members m JOIN member_financial_summary f ON m.id = f.id WHERE m.status = 'active'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quarterly Table Distribution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="admin_dashboard.php" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-left"></i> Dashboard</a>
        <a href="?download=1" class="btn btn-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> Summary</a>
    </div>
    <div class="card shadow-sm border-0 p-4">
        <h4 class="fw-bold text-primary mb-4">Table Banking Share-Out</h4>
        <?php if(isset($_SESSION['success_msg'])): ?> <div class="alert alert-success small"><?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></div> <?php endif; ?>
        <?php if(isset($_SESSION['error_msg'])): ?> <div class="alert alert-danger small"><?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?></div> <?php endif; ?>
        <table class="table align-middle">
            <thead><tr><th>Member</th><th>Dividend</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($distribution_list as $mem): $div = $mem['table_banking_balance'] * $rate; ?>
                <tr>
                    <td><?= htmlspecialchars($mem['name']) ?></td>
                    <td class="fw-bold text-primary">KSh <?= number_format($div, 2) ?></td>
                    <td>
                        <?php if($mem['paid_id']): ?> <span class="text-success small fw-bold">Paid</span>
                        <?php else: ?>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="m_id" value="<?= $mem['id'] ?>">
                            <input type="hidden" name="amt" value="<?= $div ?>">
                            <input type="hidden" name="method" value="<?= $mem['payout_preference'] ?>">
                            <?php if($mem['payout_preference'] === 'mpesa'): ?> <input type="text" name="manual_ref_code" class="form-control form-control-sm" style="width:120px" placeholder="Code" required> <?php endif; ?>
                            <button name="execute_table" class="btn btn-sm btn-primary px-3 rounded-pill">Confirm</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>