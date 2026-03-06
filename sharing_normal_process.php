<?php
require 'config.php';
require_once 'dompdf/autoload.inc.php'; 
use Dompdf\Dompdf;

// Access control for Treasury and dev roles
if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], ['Treasury', 'dev'])) {
    header('Location: profile.php'); exit;
}

// 1. Dividend Calculation Logic
$profit = $pdo->query("SELECT (SUM(penalty_accrued) + SUM(penalty)) FROM normal_loans")->fetchColumn() + 
          $pdo->query("SELECT (SUM(penalty_accrued) + SUM(penalty)) FROM uwezo_loans")->fetchColumn();

if ($profit <= 0) { $profit = 1000.00; } 

$total_weight = $pdo->query("SELECT SUM(total_dividend_weight) FROM member_financial_summary")->fetchColumn() ?: 1;
$rate = $profit / $total_weight;

// 3. Manual Payout Handler
if (isset($_POST['confirm_payout'])) {
    $m_id = $_POST['m_id']; 
    $amt = $_POST['amt']; 
    $method = $_POST['method']; 
    $userInputCode = trim($_POST['manual_ref_code'] ?? '');

    try {
        if ($amt <= 0) throw new Exception("Amount must be greater than zero.");
        $pdo->beginTransaction();
        
        $check = $pdo->prepare("SELECT id FROM share_out_history WHERE member_id = ? AND cycle_type = 'yearly' AND YEAR(share_out_date) = YEAR(CURDATE())");
        $check->execute([$m_id]);
        if ($check->fetch()) throw new Exception("Error: Member already paid for this year.");

        $final_ref = "";
        if ($method === 'mpesa') {
            if (!preg_match('/^[A-Z0-9]{10}$/', strtoupper($userInputCode))) throw new Exception("Invalid M-Pesa Code.");
            $final_ref = strtoupper($userInputCode);
        } elseif ($method === 'cash') {
            $final_ref = "CSH-" . date('Ymd') . "-" . strtoupper(substr(uniqid(), -4));
        } else {
            $final_ref = "REINV-" . date('Ymd') . "-" . $m_id;
            $pdo->prepare("INSERT INTO normal_savings (member_id, amount, transaction_type, description, transaction_date) VALUES (?, ?, 'deposit', 'Yearly Reinvestment', CURDATE())")->execute([$m_id, $amt]);
        }

        $stmt = $pdo->prepare("INSERT INTO share_out_history (member_id, cycle_type, amount_paid, payout_method, payout_status, transaction_reference, processed_by) VALUES (?, 'yearly', ?, ?, 'completed', ?, ?)");
        $stmt->execute([$m_id, $amt, $method, $final_ref, $_SESSION['member_id']]);

        $pdo->commit();

        // 4. Activity Logging
        log_admin_activity($pdo, "Yearly Payout", "Processed KSh $amt for Member ID $m_id. Ref: $final_ref");

        // 5. PRG Pattern: Redirect to prevent form resubmission
        $_SESSION['success_msg'] = "Payout recorded! Ref: $final_ref";
        header("Location: sharing_normal_process.php"); 
        exit;
    } catch (Exception $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $_SESSION['error_msg'] = $e->getMessage(); header("Location: sharing_normal_process.php"); exit; }
}

// PDF Summary Export
if (isset($_GET['download_summary'])) {
    $dompdf = new Dompdf();
    $html = '<h2 style="text-align:center;">Yearly Distribution Summary (' . date('Y') . ')</h2>';
    $html .= '<table border="1" width="100%" style="border-collapse: collapse; font-size: 12px;"><thead><tr><th>Member</th><th>Amount</th><th>Method</th><th>Reference</th></tr></thead><tbody>';
    $records = $pdo->query("SELECT s.*, m.name FROM share_out_history s JOIN members m ON s.member_id = m.id WHERE cycle_type = 'yearly' AND YEAR(share_out_date) = YEAR(CURDATE())")->fetchAll();
    foreach ($records as $r) { $html .= '<tr><td>' . htmlspecialchars($r['name']) . '</td><td>KSh ' . number_format($r['amount_paid'], 2) . '</td><td>' . strtoupper($r['payout_method']) . '</td><td>' . $r['transaction_reference'] . '</td></tr>'; }
    $html .= '</tbody></table>';
    $dompdf->loadHtml($html); $dompdf->setPaper('A4', 'portrait'); $dompdf->render();
    $dompdf->stream("Yearly_ShareOut_" . date('Y') . ".pdf"); exit;
}

$distribution_list = $pdo->query("SELECT m.id, m.name, m.phone, m.payout_preference, f.total_dividend_weight, (SELECT payout_status FROM share_out_history WHERE member_id = m.id AND cycle_type = 'yearly' AND YEAR(share_out_date) = YEAR(CURDATE())) as status FROM members m JOIN member_financial_summary f ON m.id = f.id WHERE m.status = 'active'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Yearly Distribution • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="admin_dashboard.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
        <a href="?download_summary=1" class="btn btn-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF Summary</a>
    </div>
    <div class="card shadow-sm border-0 p-4">
        <h4 class="fw-bold mb-4">Yearly Payout Console</h4>
        <?php if(isset($_SESSION['success_msg'])): ?> <div class="alert alert-success small"><?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></div> <?php endif; ?>
        <?php if(isset($_SESSION['error_msg'])): ?> <div class="alert alert-danger small"><?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?></div> <?php endif; ?>
        <table class="table align-middle">
            <thead><tr><th>Member</th><th>Dividend</th><th>Method</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($distribution_list as $mem): $div = $mem['total_dividend_weight'] * $rate; ?>
                <tr>
                    <td><?= htmlspecialchars($mem['name']) ?></td>
                    <td class="fw-bold text-success">KSh <?= number_format($div, 2) ?></td>
                    <td><span class="badge bg-dark"><?= strtoupper($mem['payout_preference']) ?></span></td>
                    <td>
                        <?php if($mem['status'] == 'completed'): ?> <span class="text-success small fw-bold">Paid</span>
                        <?php else: ?>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="m_id" value="<?= $mem['id'] ?>">
                            <input type="hidden" name="amt" value="<?= $div ?>">
                            <input type="hidden" name="method" value="<?= $mem['payout_preference'] ?>">
                            <?php if($mem['payout_preference'] === 'mpesa'): ?> <input type="text" name="manual_ref_code" class="form-control form-control-sm" style="width:120px" placeholder="Code" required> <?php endif; ?>
                            <button name="confirm_payout" class="btn btn-sm btn-primary px-3 rounded-pill">Confirm</button>
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