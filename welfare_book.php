<?php
require 'config.php';

// --- 1. Security Check ---
if (!isset($_SESSION['member_id']) || !isset($_SESSION['paid']) || $_SESSION['paid'] !== 1) {
    header('Location: login.php');
    exit;
}

$member_id = $_SESSION['member_id'];

try {
    // 2. Fetch Welfare Balance from Summary View
    $stmt = $pdo->prepare("SELECT welfare_balance FROM member_financial_summary WHERE id = ?");
    $stmt->execute([$member_id]);
    $fin = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Fetch contributions (Ledger)
    $stmt = $pdo->prepare("SELECT * FROM welfare_contributions WHERE member_id = ? ORDER BY contribution_date DESC");
    $stmt->execute([$member_id]);
    $contributions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Fetch welfare requests (Claims)
    $stmt = $pdo->prepare("SELECT * FROM welfare_requests WHERE member_id = ? ORDER BY created_at DESC");
    $stmt->execute([$member_id]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Calculate lifetime contributions for stats
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM welfare_contributions WHERE member_id = ?");
    $stmt->execute([$member_id]);
    $lifetime_contrib = $stmt->fetchColumn() ?: 0;

} catch (PDOException $e) {
    // Displays the technical error if it still refuses to load
    die("Database Error: " . $e->getMessage());
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
    <title>Welfare Book • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --welfare-purple: #6f42c1;
            --bg-soft: #f4f0fa;
        }

        body {
            background-color: var(--bg-soft);
            font-family: 'Inter', sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--welfare-purple) !important;
        }

        .glass-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .summary-box {
            background: linear-gradient(135deg, #6f42c1, #a951ed);
            color: white;
            text-align: center;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-transparent mb-3 mt-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="profile.php">
                <i class="bi bi-chevron-left me-2"></i>Welfare Book
            </a>
            <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
                <a href="profile.php" class="btn btn-sm btn-outline-secondary rounded-pill">Dashboard</a>
                <a href="normal_book.php" class="btn btn-sm btn-outline-success rounded-pill">Normal</a>
                <a href="table_banking_book.php" class="btn btn-sm btn-outline-warning rounded-pill">Table Banking</a>
                <a href="loan_statement.php" class="btn btn-sm btn-outline-danger rounded-pill">Loan Statement</a>
                <a href="welfare.php" class="btn btn-sm btn-outline-primary rounded-pill">Welfare Info</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="glass-card summary-box shadow">
            <small class="text-uppercase opacity-75 fw-bold">My Current Welfare Balance</small>
            <h1 class="display-5 fw-bold mb-0"><?= format_currency($fin['welfare_balance'] ?? 0) ?></h1>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="glass-card" style="border-left: 5px solid var(--welfare-purple);">
                    <h6 class="fw-bold mb-3 text-uppercase text-muted small">Standard Benefits</h6>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between bg-transparent px-0">Death of Member
                            <span>KSh 3,000</span></li>
                        <li class="list-group-item d-flex justify-content-between bg-transparent px-0">Death of Parent
                            <span>KSh 2,000</span></li>
                        <li class="list-group-item d-flex justify-content-between bg-transparent px-0">Death of Child
                            <span>KSh 2,000</span></li>
                    </ul>
                </div>

                <div class="glass-card">
                    <h5 class="fw-bold mb-4">Claim History</h5>
                    <?php if (empty($requests)): ?>
                        <p class="text-muted small">No claims requested yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $req): ?>
                                        <tr>
                                            <td class="small"><?= ucfirst(str_replace('_', ' ', $req['request_type'])) ?></td>
                                            <td>
                                                <span
                                                    class="status-badge badge bg-<?= $req['status'] == 'paid' ? 'success' : 'warning' ?>">
                                                    <?= strtoupper($req['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Contribution Ledger</h5>
                        <small class="text-muted">Lifetime: <?= format_currency($lifetime_contrib) ?></small>
                    </div>
                    <?php if (empty($contributions)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-folder2-open display-4 text-muted"></i>
                            <p class="text-muted mt-2">No records found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contributions as $c): ?>
                                        <tr>
                                            <td class="small"><?= date('d M, Y', strtotime($c['contribution_date'])) ?></td>
                                            <td>
                                                <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3">
                                                    <?= ucfirst($c['contribution_type']) ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold"><?= format_currency($c['amount']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>