<?php
require 'config.php';

if (!isset($_SESSION['member_id']) || !isset($_SESSION['paid']) || $_SESSION['paid'] !== 1) {
    header('Location: login.php');
    exit;
}

$member_id = $_SESSION['member_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM member_financial_summary WHERE id = ?");
    $stmt->execute([$member_id]);
    $fin = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM normal_savings WHERE member_id = ? ORDER BY transaction_date DESC");
    $stmt->execute([$member_id]);
    $savings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM weekly_contributions WHERE member_id = ? ORDER BY year DESC, week_number DESC");
    $stmt->execute([$member_id]);
    $weeklies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM normal_loans WHERE member_id = ? ORDER BY request_date DESC");
    $stmt->execute([$member_id]);
    $normal_loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("System Error: " . $e->getMessage());
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
    <title>Normal Book • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a5928;
            --bg: #f4f7f6;
        }

        body {
            background: var(--bg);
            font-family: 'Inter', sans-serif;
        }

        .summary-banner {
            background: linear-gradient(135deg, var(--primary), #2d8a43);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .content-card {
            background: white;
            border-radius: 18px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }

        .status-paid {
            background: #d1e7dd;
            color: #0f5132;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-transparent mb-3 mt-2">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="profile.php"><i
                    class="bi bi-chevron-left me-2"></i>Normal Book</a>
            <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
                <a href="profile.php" class="btn btn-sm btn-outline-secondary rounded-pill">Dashboard</a>
                <a href="loan_statement.php" class="btn btn-sm btn-outline-danger rounded-pill">Loan Statement</a>
                <a href="table_banking_book.php" class="btn btn-sm btn-outline-warning rounded-pill">Table Banking</a>
                <a href="welfare_book.php" class="btn btn-sm btn-outline-primary rounded-pill">Welfare</a>
                <span class="text-muted small d-none d-md-inline">Updated: <?= date('d M, Y') ?></span>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="summary-banner shadow">
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3">
                    <small class="opacity-75 d-block mb-1">Total Savings</small>
                    <h4 class="fw-bold"><?= format_currency($fin['normal_savings_balance'] ?? 0) ?></h4>
                </div>
                <div class="col-6 col-md-3">
                    <small class="opacity-75 d-block mb-1">Loan Limit (2x)</small>
                    <h4 class="fw-bold"><?= format_currency(($fin['normal_savings_balance'] ?? 0) * 2) ?></h4>
                </div>
                <div class="col-6 col-md-3">
                    <small class="opacity-75 d-block mb-1">Weekly Paid</small>
                    <h4 class="fw-bold"><?= format_currency($fin['total_weekly_paid'] ?? 0) ?></h4>
                </div>
                <div class="col-6 col-md-3">
                    <small class="opacity-75 d-block mb-1">Debt Balance</small>
                    <h4 class="fw-bold"><?= format_currency($fin['normal_loans_balance'] ?? 0) ?></h4>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h5 class="fw-bold mb-4 text-success"><i class="bi bi-calendar-check me-2"></i>Weekly Contributions</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Year/Week</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($weeklies as $w): ?>
                            <tr>
                                <td><strong><?= $w['year'] ?></strong> Week <?= $w['week_number'] ?></td>
                                <td><?= format_currency($w['amount']) ?></td>
                                <td><span class="status-paid"><?= strtoupper($w['status']) ?></span></td>
                                <td class="text-muted small"><?= $w['paid_date'] ?? '---' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-card">
            <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-cash-coin me-2"></i>Normal Loan History</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Borrowed</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($normal_loans as $l): ?>
                            <tr>
                                <td><?= date('d/m/y', strtotime($l['request_date'])) ?></td>
                                <td class="fw-bold"><?= format_currency($l['amount']) ?></td>
                                <td class="text-danger fw-bold"><?= format_currency($l['balance'] ?? 0) ?></td>
                                <td>
                                    <span
                                        class="badge rounded-pill bg-<?= $l['status'] == 'paid' ? 'success' : ($l['status'] == 'active' ? 'primary' : 'warning') ?>">
                                        <?= ucfirst($l['status']) ?>
                                    </span>
                                </td>
                                <td class="small text-muted"><?= $l['due_date'] ?? '---' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>