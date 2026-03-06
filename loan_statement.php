<?php
require 'config.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: login.php');
    exit;
}

$member_id = $_SESSION['member_id'];

// --- PDF DOWNLOAD LOGIC ---
if (isset($_GET['download_pdf'])) {
    $pdfPath = __DIR__ . '/dompdf/autoload.inc.php'; 
    if (file_exists($pdfPath)) {
        require_once $pdfPath;
        $dompdf = new \Dompdf\Dompdf();

        // Data Fetch for PDF
        $stmt_loans = $pdo->prepare("
            SELECT amount, request_date, 'Normal' as type FROM normal_loans WHERE member_id = ? AND status IN ('approved', 'active')
            UNION ALL
            SELECT amount, request_date, 'Table Banking' as type FROM table_banking_loans WHERE member_id = ? AND status IN ('approved', 'active')
            UNION ALL
            SELECT amount, request_date, 'Uwezo' as type FROM uwezo_loans WHERE member_id = ? AND status IN ('approved', 'active')
        ");
        $stmt_loans->execute([$member_id, $member_id, $member_id]);
        $pdf_loans = $stmt_loans->fetchAll();

        $stmt_reps = $pdo->prepare("SELECT * FROM loan_repayments WHERE member_id = ? ORDER BY repayment_date DESC");
        $stmt_reps->execute([$member_id]);
        $pdf_reps = $stmt_reps->fetchAll();

        $stmt_summ = $pdo->prepare("SELECT * FROM member_financial_summary WHERE id = ?");
        $stmt_summ->execute([$member_id]);
        $pdf_summary = $stmt_summ->fetch();

        $html = "
        <style>
            body { font-family: sans-serif; color: #333; font-size: 11px; }
            .header { text-align: center; border-bottom: 2px solid #1a5928; padding-bottom: 10px; margin-bottom: 20px; }
            h2 { color: #1a5928; margin: 0; }
            .summary { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background: #1a5928; color: white; padding: 8px; text-align: left; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            .total-row { font-weight: bold; background: #eee; }
        </style>
        <div class='header'>
            <h2>Jera Moyie Official Loan Statement</h2>
            <p>Generated for: " . $_SESSION['name'] . " | Date: " . date('d M Y') . "</p>
        </div>
        <div class='summary'>
            <strong>Total Outstanding Debt: KSh " . number_format($pdf_summary['normal_loans_balance'] + $pdf_summary['table_banking_loans_balance'] + $pdf_summary['uwezo_loans_balance'], 2) . "</strong>
        </div>
        <h3>Active Loans (Principal)</h3>
        <table>
            <thead><tr><th>Date</th><th>Loan Type</th><th>Approved Amount</th></tr></thead>
            <tbody>";
        foreach ($pdf_loans as $l) {
            $html .= "<tr><td>{$l['request_date']}</td><td>{$l['type']}</td><td>KSh " . number_format($l['amount'], 2) . "</td></tr>";
        }
        $html .= "</tbody></table>
        <h3 style='margin-top:20px;'>Repayment History</h3>
        <table>
            <thead><tr><th>Date</th><th>Type</th><th>Ref #</th><th>Amount Paid</th></tr></thead>
            <tbody>";
        foreach ($pdf_reps as $r) {
            $html .= "<tr><td>{$r['repayment_date']}</td><td>" . ucfirst($r['loan_type']) . "</td><td>{$r['reference_number']}</td><td>KSh " . number_format($r['amount'], 2) . "</td></tr>";
        }
        $html .= "</tbody></table>";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Loan_Statement_" . date('Y-m-d') . ".pdf");
        exit;
    }
}

try {
    // Standard page data fetch
    $stmt = $pdo->prepare("
        SELECT id, amount, request_date, 'Normal' as type FROM normal_loans WHERE member_id = ? AND status IN ('approved', 'active')
        UNION ALL
        SELECT id, amount, request_date, 'Table Banking' as type FROM table_banking_loans WHERE member_id = ? AND status IN ('approved', 'active')
        UNION ALL
        SELECT id, amount, request_date, 'Uwezo' as type FROM uwezo_loans WHERE member_id = ? AND status IN ('approved', 'active')
    ");
    $stmt->execute([$member_id, $member_id, $member_id]);
    $active_loans = $stmt->fetchAll();

    $stmt_rep = $pdo->prepare("SELECT * FROM loan_repayments WHERE member_id = ? ORDER BY repayment_date DESC");
    $stmt_rep->execute([$member_id]);
    $repayments = $stmt_rep->fetchAll();

    $stmt_sum = $pdo->prepare("SELECT * FROM member_financial_summary WHERE id = ?");
    $stmt_sum->execute([$member_id]);
    $summary = $stmt_sum->fetch();

} catch (PDOException $e) {
    die("Statement Error: " . $e->getMessage());
}

function format_currency($amount) { return 'KSh ' . number_format($amount, 2); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Statement • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-3 mt-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="profile.php">
                <i class="bi bi-chevron-left me-2"></i>Loan Statement
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="profile.php" class="btn btn-sm btn-outline-secondary rounded-pill">Dashboard</a>
                <a href="loan_request.php" class="btn btn-sm btn-outline-success rounded-pill">Request Loan</a>
            </div>
        </div>
    </nav>

    <div class="container pt-4">
        <div class="hero-card mb-5 mt-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <small class="text-uppercase fw-bold opacity-75">Combined Loan Balance</small>
                    <h1 class="display-4 fw-bold mt-1 mb-0"><?= format_currency($summary['normal_loans_balance'] + $summary['table_banking_loans_balance'] + $summary['uwezo_loans_balance']) ?></h1>
                    <p class="lead opacity-75 mt-2">Total Outstanding Debt across all products</p>
                </div>
                <div class="col-md-5 text-md-end mt-4 mt-md-0">
                    <a href="?download_pdf=1" class="btn btn-light rounded-pill px-4 py-2 shadow-sm text-success fw-bold">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-5">
                <div class="glass-card mb-4 h-100">
                    <h5 class="fw-bold mb-4 d-flex align-items-center">
                        <i class="bi bi-lightning-charge me-2 text-primary"></i>Active Loans
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle border-0">
                            <thead class="bg-light small">
                                <tr><th>Type</th><th>Principal</th><th class="text-end">Date</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($active_loans as $l): ?>
                                <tr>
                                    <td class="fw-bold"><?= $l['type'] ?></td>
                                    <td class="fw-bold text-danger"><?= format_currency($l['amount']) ?></td>
                                    <td class="text-end small text-muted"><?= date('d/m/y', strtotime($l['request_date'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($active_loans)): ?>
                                    <tr><td colspan="3" class="text-center py-4 text-muted small">No active loans.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="glass-card h-100">
                    <h5 class="fw-bold mb-4 d-flex align-items-center">
                        <i class="bi bi-clock-history me-2 text-success"></i>Repayment History
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0">
                            <thead class="bg-light small">
                                <tr><th>Date</th><th>Loan Type</th><th class="text-end">Amount Paid</th></tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($repayments)): ?>
                                    <?php foreach ($repayments as $r): ?>
                                        <tr>
                                            <td><?= date('d/m/y', strtotime($r['repayment_date'])) ?></td>
                                            <td><?= isset($r['loan_type']) ? ucfirst($r['loan_type']) : 'Loan' ?></td>
                                            <td class="text-end fw-bold text-success"><?= format_currency($r['amount']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">No repayments recorded yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>