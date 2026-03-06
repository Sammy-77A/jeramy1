<?php
require 'config.php';

/**
 * SECURITY & PERMISSIONS
 * Restricted to Treasury, Chairperson, Trustee, or Dev.
 */
$allowed_roles = ['Treasury', 'Chairperson', 'Trustee', 'dev'];
if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: profile.php');
    exit;
}

$can_edit = ($_SESSION['role'] === 'Treasury' || $_SESSION['role'] === 'dev');

// === COMPREHENSIVE PDF EXPORT LOGIC ===
if (isset($_GET['export_pdf'])) {
    $pdfPath = __DIR__ . '/dompdf/autoload.inc.php'; 
    if (file_exists($pdfPath)) {
        require_once $pdfPath;
        $dompdf = new \Dompdf\Dompdf();
        
        // Fetch Detailed Master Ledger
        $stmt = $pdo->query("SELECT m.name, f.* FROM members m JOIN member_financial_summary f ON m.id = f.id ORDER BY m.name ASC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = '
        <style>
            body { font-family: "Helvetica", sans-serif; color: #222; font-size: 8px; }
            .header { text-align: center; border-bottom: 2px solid #1a5928; padding-bottom: 10px; margin-bottom: 15px; }
            h1 { color: #1a5928; margin: 0; font-size: 14px; text-transform: uppercase; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background: #1a5928; color: white; padding: 4px; text-align: left; border: 1px solid #1a5928; }
            td { padding: 4px; border: 1px solid #ddd; }
            .section-head { background: #f4f4f4; font-weight: bold; padding: 5px; margin-top: 15px; border-left: 4px solid #1a5928; font-size: 9px; }
            .debt { color: #dc3545; font-weight: bold; }
        </style>
        <div class="header">
            <h1>JERA MOYIE STRATEGIC AUDIT LEDGER</h1>
            <p>Master Financial Report • Generated: ' . date('d M Y, H:i') . '</p>
        </div>

        <div class="section-head">MEMBER SAVINGS & CONTRIBUTIONS BREAKDOWN</div>
        <table>
            <thead>
                <tr>
                    <th>Member Name</th>
                    <th>Weekly Paid</th>
                    <th>Normal Savings</th>
                    <th>Table Bank Shares</th>
                    <th>Welfare Fund</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($data as $row) {
            $html .= "<tr>
                <td><strong>{$row['name']}</strong></td>
                <td>KSh " . number_format($row['total_weekly_paid'], 2) . "</td>
                <td>KSh " . number_format($row['normal_savings_balance'], 2) . "</td>
                <td>KSh " . number_format($row['table_banking_balance'], 2) . "</td>
                <td>KSh " . number_format($row['welfare_balance'], 2) . "</td>
            </tr>";
        }
        $html .= '</tbody></table>

        <div class="section-head">ACTIVE LOAN BALANCES & GROUP EXPOSURE</div>
        <table>
            <thead>
                <tr>
                    <th>Member Name</th>
                    <th>Normal Loan</th>
                    <th>Table Loan</th>
                    <th>Uwezo Loan</th>
                    <th>OUTSTANDING DEBT</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($data as $row) {
            $total_d = $row['normal_loans_balance'] + $row['table_banking_loans_balance'] + $row['uwezo_loans_balance'];
            $html .= "<tr>
                <td>{$row['name']}</td>
                <td>" . number_format($row['normal_loans_balance'], 0) . "</td>
                <td>" . number_format($row['table_banking_loans_balance'], 0) . "</td>
                <td>" . number_format($row['uwezo_loans_balance'], 0) . "</td>
                <td class='".($total_d > 0 ? 'debt' : '')."'>KSh " . number_format($total_d, 2) . "</td>
            </tr>";
        }
        $html .= '</tbody></table>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("JeraMoyie_Strategic_Audit_" . date('Y-m-d') . ".pdf");
        exit;
    }
}

// --- 1. HANDLE MANUAL CONTRIBUTION ENTRY ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manual_entry'])) {
    if ($can_edit) {
        $mid = $_POST['member_id'];
        $amt = (float)$_POST['amount'];
        $type = $_POST['ledger_type'];
        $ref = "CASH-" . time();

        try {
            $pdo->beginTransaction();

            if ($type === 'welfare') {
                $stmt = $pdo->prepare("INSERT INTO welfare_contributions (member_id, amount, contribution_date, description) VALUES (?, ?, NOW(), ?)");
                $stmt->execute([$mid, $amt, "Manual Meeting Entry: $ref"]);
            } elseif ($type === 'weekly') {
                $stmt = $pdo->prepare("INSERT INTO weekly_contributions (member_id, amount, week_number, year, contribution_date, status, paid_date) VALUES (?, ?, ?, ?, CURDATE(), 'paid', NOW())");
                $stmt->execute([$mid, $amt, date('W'), date('Y')]);
            } elseif ($type === 'loan_repayment') {
                // SPILL-OVER LOGIC: Get total active debt
                $stmt_debt = $pdo->prepare("SELECT (normal_loans_balance + table_banking_loans_balance + uwezo_loans_balance) as total_debt FROM member_financial_summary WHERE id = ?");
                $stmt_debt->execute([$mid]);
                $current_debt = (float)$stmt_debt->fetchColumn();

                if ($amt > $current_debt && $current_debt > 0) {
                    $excess = $amt - $current_debt;
                    // Pay off the debt exactly
                    $stmt = $pdo->prepare("INSERT INTO loan_repayments (member_id, amount, repayment_date, loan_type, reference_number, notes) VALUES (?, ?, CURDATE(), 'normal', ?, ?)");
                    $stmt->execute([$mid, $current_debt, $ref, "Debt Cleared. Spillover detected."]);
                    
                    // Move excess to Normal Savings [cite: 18]
                    $stmt_bal = $pdo->prepare("SELECT balance_after FROM normal_savings WHERE member_id = ? ORDER BY id DESC LIMIT 1");
                    $stmt_bal->execute([$mid]);
                    $new_sav_bal = ((float)$stmt_bal->fetchColumn() ?: 0) + $excess;
                    
                    $stmt_sav = $pdo->prepare("INSERT INTO normal_savings (member_id, amount, transaction_type, transaction_date, balance_after, description) VALUES (?, ?, 'deposit', NOW(), ?, ?)");
                    $stmt_sav->execute([$mid, $excess, $new_sav_bal, "Auto-credit: Loan overpayment spill-over"]);
                    $_SESSION['success'] = "Loan cleared! Excess KSh ".number_format($excess)." moved to Savings.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO loan_repayments (member_id, amount, repayment_date, loan_type, reference_number, notes) VALUES (?, ?, CURDATE(), 'normal', ?, ?)");
                    $stmt->execute([$mid, $amt, $ref, "Manual Meeting Repayment"]);
                    $_SESSION['success'] = "Repayment recorded successfully.";
                }
            } else {
                // Standard Savings/Shares logic [cite: 15, 18]
                $tbl = ($type === 'normal_savings') ? 'normal_savings' : 'table_banking_shares';
                $stmt_bal = $pdo->prepare("SELECT balance_after FROM $tbl WHERE member_id = ? ORDER BY id DESC LIMIT 1");
                $stmt_bal->execute([$mid]);
                $bal = ((float)$stmt_bal->fetchColumn() ?: 0) + $amt;

                $stmt = $pdo->prepare("INSERT INTO $tbl (member_id, amount, transaction_type, transaction_date, balance_after, description) VALUES (?, ?, ?, NOW(), ?, ?)");
                $t_type = ($type === 'normal_savings') ? 'deposit' : 'share';
                $stmt->execute([$mid, $amt, $t_type, $bal, "Manual Meeting Entry: $ref"]);
                $_SESSION['success'] = "Contribution recorded successfully.";
            }

            $pdo->commit();
            log_admin_activity($pdo, "Manual Financial Entry", "Member ID: $mid, Amount: KSh $amt, Type: $type, Ref: $ref");
        } catch (Exception $e) { 
            $pdo->rollBack(); 
            $_SESSION['error'] = "Database Error: " . $e->getMessage(); 
        }
    } else { $_SESSION['error'] = "Unauthorized: Treasury only."; }
    
    header("Location: admin_finance.php");
    exit;
}

// --- 2. FETCH POOL TOTALS ---
$sum_stmt = $pdo->query("SELECT SUM(normal_savings_balance) as ns, SUM(table_banking_balance) as tb, SUM(welfare_balance) as wf, SUM(total_weekly_paid) as wk FROM member_financial_summary");
$pools = $sum_stmt->fetch(PDO::FETCH_ASSOC);

// --- 3. FETCH LIVE LEDGER ---
$stmt = $pdo->query("SELECT m.id, m.name, f.* FROM members m JOIN member_financial_summary f ON m.id = f.id ORDER BY m.name ASC");
$all_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

function format_currency($amount) { return 'KSh ' . number_format($amount, 2); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Treasury • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #1a5928; --bg: #f3f5f7; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; padding-top: 30px; }
        .stat-card { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .ledger-val { font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="fw-bold mb-0">Group Financial Oversight</h2><p class="text-muted small">Treasury Management Console</p></div>
        <div class="d-flex gap-2">
            <?php if($can_edit): ?><button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#manualModal">Record Meeting Cash</button><?php endif; ?>
            <a href="?export_pdf=1" class="btn btn-danger rounded-pill px-4 shadow-sm"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Export Strategic Ledger</a>
            <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-pill px-4"><i class="bi bi-chevron-left me-1"></i>Console</a>
            <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-4">Profile</a>
        </div>
    </div>

    <div class="row g-3 mb-4 text-center">
        <div class="col-md-3"><div class="card stat-card p-3 bg-white border-start border-4 border-success"><small class="text-muted fw-bold">NORMAL SAVINGS</small><h5 class="fw-bold text-success"><?= format_currency($pools['ns'] ?? 0) ?></h5></div></div>
        <div class="col-md-3"><div class="card stat-card p-3 bg-white border-start border-4 border-info"><small class="text-muted fw-bold">TABLE BANKING</small><h5 class="fw-bold text-info"><?= format_currency($pools['tb'] ?? 0) ?></h5></div></div>
        <div class="col-md-3"><div class="card stat-card p-3 bg-white border-start border-4 border-primary"><small class="text-muted fw-bold">WELFARE FUND</small><h5 class="fw-bold text-primary"><?= format_currency($pools['wf'] ?? 0) ?></h5></div></div>
        <div class="col-md-3"><div class="card stat-card p-3 bg-white border-start border-4 border-warning"><small class="text-muted fw-bold">WEEKLY POOL</small><h5 class="fw-bold text-warning"><?= format_currency($pools['wk'] ?? 0) ?></h5></div></div>
    </div>

    <?php if(isset($_SESSION['success'])): ?><div class="alert alert-success border-0 shadow-sm rounded-3 mb-3"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div><?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?><div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div><?php endif; ?>

    <div class="table-card">
        <h5 class="fw-bold mb-4">Master Ledger Registry (Live)</h5>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr><th>Member</th><th>Weekly</th><th>Normal Sav</th><th>Table Shares</th><th>Welfare</th><th>Active Debt</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($all_members as $m): 
                        $row_debt = $m['normal_loans_balance'] + $m['table_banking_loans_balance'] + $m['uwezo_loans_balance'];
                    ?>
                    <tr>
                        <td><a href="admin_member_profile.php?id=<?= $m['id'] ?>" class="text-decoration-none text-dark"><strong><?= htmlspecialchars($m['name']) ?></strong></a><br><small class="text-muted">#<?= str_pad($m['id'], 3, '0', STR_PAD_LEFT) ?></small></td>
                        <td class="ledger-val"><?= format_currency($m['total_weekly_paid']) ?></td>
                        <td class="ledger-val"><?= format_currency($m['normal_savings_balance']) ?></td>
                        <td class="ledger-val"><?= format_currency($m['table_banking_balance']) ?></td>
                        <td class="ledger-val"><?= format_currency($m['welfare_balance']) ?></td>
                        <td class="text-danger fw-bold ledger-val"><?= format_currency($row_debt) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if($can_edit): ?>
<div class="modal fade" id="manualModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4 shadow-lg">
            <h5 class="fw-bold mb-3">Record Official Cash Entry</h5>
            <form method="POST">
                <div class="mb-3"><label class="small fw-bold">Select Member</label><select name="member_id" class="form-select border-2"><?php foreach($all_members as $m) echo "<option value='{$m['id']}'>{$m['name']}</option>"; ?></select></div>
                <div class="mb-3">
                    <label class="small fw-bold">Payment Category</label>
                    <select name="ledger_type" class="form-select border-2">
                        <option value="normal_savings">Normal Savings Deposit</option>
                        <option value="table_banking">Table Banking Shares</option>
                        <option value="weekly">Weekly Contribution (3K)</option>
                        <option value="welfare">Welfare Fund Contribution</option>
                        <option value="loan_repayment">Loan Repayment</option>
                    </select>
                </div>
                <div class="mb-3"><label class="small fw-bold">Amount (KSh)</label><input type="number" name="amount" class="form-control border-2" placeholder="e.g. 3000" required></div>
                <button type="submit" name="manual_entry" class="btn btn-primary w-100 py-2 rounded-3 shadow">Commit to Ledger</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>