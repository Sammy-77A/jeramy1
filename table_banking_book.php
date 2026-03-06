<?php
// table_banking_book.php
require 'config.php';

if (!isset($_SESSION['member_id']) || !isset($_SESSION['paid']) || $_SESSION['paid'] !== 1) {
    header('Location: login.php');
    exit;
}

$member_id = $_SESSION['member_id'];

try {
    // 1. Fetch Summary
    $stmt = $pdo->prepare("SELECT table_banking_balance, table_banking_loans_balance FROM member_financial_summary WHERE id = ?");
    $stmt->execute([$member_id]);
    $fin = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Fetch Shares (Mirrors Ledger: Date, Share Contr, Total Share)
    $stmt = $pdo->prepare("SELECT * FROM table_banking_shares WHERE member_id = ? ORDER BY transaction_date DESC");
    $stmt->execute([$member_id]);
    $shares = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch Loans (Repayment in 3 months)
    $stmt = $pdo->prepare("SELECT * FROM table_banking_loans WHERE member_id = ? ORDER BY request_date DESC");
    $stmt->execute([$member_id]);
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("System Error: Unable to load records.");
}

function format_currency($amount)
{
    return 'KSh ' . number_format($amount, 2);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Banking • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --accent: #f59e0b;
            --bg: #fffcf5;
        }

        body {
            background: var(--bg);
        }

        .ledger-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #ffeeba;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .table-ledger thead {
            background: #fff3cd;
            color: #856404;
        }

        .cycle-badge {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-transparent mb-3 mt-2">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="profile.php"><i class="bi bi-chevron-left me-2"></i>Table
                Banking</a>
            <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
                <a href="profile.php" class="btn btn-sm btn-outline-secondary rounded-pill">Dashboard</a>
                <a href="normal_book.php" class="btn btn-sm btn-outline-success rounded-pill">Normal Book</a>
                <a href="loan_statement.php" class="btn btn-sm btn-outline-danger rounded-pill">Loan Statement</a>
                <a href="welfare_book.php" class="btn btn-sm btn-outline-primary rounded-pill">Welfare</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="cycle-badge">
            <i class="bi bi-arrow-repeat me-2"></i> <strong>Current Cycle:</strong> 3-Month Sharing (Ends March 2026)
        </div>

        <div class="ledger-card p-4 mb-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-book-half me-2"></i>Shares & Loans Ledger</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-ledger align-middle text-center">
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>SHARES CONTR.</th>
                            <th>LOAN TAKEN</th>
                            <th>INTEREST</th>
                            <th>TOTAL</th>
                            <th>BAL.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shares as $s): ?>
                            <tr>
                                <td><?= date('d/m', strtotime($s['transaction_date'])) ?></td>
                                <td class="fw-bold"><?= format_currency($s['amount']) ?></td>
                                <td>---</td>
                                <td>---</td>
                                <td>---</td>
                                <td class="bg-light fw-bold"><?= format_currency($s['balance_after']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-card bg-white p-4 rounded-3 shadow-sm">
            <h6 class="fw-bold mb-3">Active Table Loans (3 Months Repayment)</h6>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $l): ?>
                        <tr>
                            <td><?= date('d/m/y', strtotime($l['request_date'])) ?></td>
                            <td><?= format_currency($l['amount']) ?></td>
                            <td><?= ucfirst($l['status']) ?></td>
                            <td><strong><?= $l['due_date'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>