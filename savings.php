<?php
require 'config.php';

if (!isset($_SESSION['member_id']) || $_SESSION['paid'] !== 1) {
    header('Location: login.php');
    exit;
}

$member_id = $_SESSION['member_id'];

// Fetch Member Data
$stmt = $pdo->prepare("SELECT name FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch();

// Fetch Transactions & Calculate Running Balance
$stmt = $pdo->prepare("SELECT * FROM savings_transactions WHERE member_id = ? ORDER BY date ASC, id ASC");
$stmt->execute([$member_id]);
$raw_transactions = $stmt->fetchAll();

$balance = 0;
$history = [];
$total_deposits = 0;
$total_withdrawals = 0;

foreach ($raw_transactions as $t) {
    $amt = (float)$t['amount'];
    if (in_array($t['type'], ['deposit', 'repayment'])) {
        $balance += $amt;
        if ($t['type'] === 'deposit') $total_deposits += $amt;
    } else {
        $balance -= $amt;
        $total_withdrawals += $amt;
    }
    $t['running_balance'] = $balance;
    $history[] = $t;
}
$history = array_reverse($history);

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Savings • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2c7a4b; --primary-dark: #1e5a38; --accent: #f59e0b; --bg-light: #f8f9fa; }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; padding-top: 80px; }
        h1, h2, h5 { font-family: 'Playfair Display', serif; color: var(--primary); }
        
        .navbar { background: white; box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        .navbar-brand { font-weight: 700; color: var(--primary) !important; }

        .hero-card { 
            background: linear-gradient(135deg, #1a5f3d, #2c7a4b); 
            color: white; border-radius: 20px; padding: 2.5rem; 
            position: relative; overflow: hidden;
            box-shadow: 0 10px 30px rgba(44, 122, 75, 0.25);
        }
        .hero-card::after {
            content: '\F615'; font-family: 'bootstrap-icons'; position: absolute;
            right: -20px; bottom: -40px; font-size: 10rem; opacity: 0.1; transform: rotate(-20deg);
        }

        .stat-card { 
            background: white; border: none; border-radius: 16px; 
            padding: 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); 
            height: 100%; transition: transform 0.2s;
        }
        .icon-box {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: 1rem;
        }
        .icon-deposit { background: #e8f5e9; color: var(--primary); }
        .icon-withdraw { background: #f8d7da; color: #dc3545; }

        .history-card { background: white; border-radius: 16px; border: none; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); }
        .badge-deposit { background: #d1e7dd; color: #0f5132; }
        .badge-withdraw { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="profile.php"><i class="bi bi-grid-fill me-2"></i>Jera Moyie</a>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="profile.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="savings.php">Savings</a></li>
                    <li class="nav-item"><a class="nav-link" href="request_loan.php">Loans</a></li>
                    <li class="nav-item"><a class="nav-link" href="welfare.php">Welfare</a></li>
                    <li class="nav-item ms-lg-2"><a href="?logout=1" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php if (isset($_SESSION['stk_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4">
                <i class="bi bi-phone me-2"></i><?= $_SESSION['stk_success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['stk_success']); ?>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="hero-card mb-4">
                    <h3 class="text-white mb-2">Total Savings</h3>
                    <h1 class="display-5 fw-bold text-white mb-4">KSh <?= number_format($balance) ?></h1>
                    <button class="btn btn-warning rounded-pill w-100 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#depositModal">
                        <i class="bi bi-plus-circle-fill me-2"></i> Deposit Now
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-card text-center">
                            <div class="icon-box icon-deposit mx-auto"><i class="bi bi-arrow-down-left-circle"></i></div>
                            <h5 class="fw-bold mb-0">KSh <?= number_format($total_deposits) ?></h5>
                            <small class="text-muted">Total In</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card text-center">
                            <div class="icon-box icon-withdraw mx-auto"><i class="bi bi-arrow-up-right-circle"></i></div>
                            <h5 class="fw-bold mb-0">KSh <?= number_format($total_withdrawals) ?></h5>
                            <small class="text-muted">Total Out</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="history-card">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold mb-0">Transaction History</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end pe-4">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $t): ?>
                                <tr>
                                    <td class="ps-4 small text-muted"><?= date('M d, Y', strtotime($t['date'])) ?></td>
                                    <td><span class="badge rounded-pill <?= in_array($t['type'], ['deposit','repayment']) ? 'badge-deposit' : 'badge-withdraw' ?>"><?= ucfirst($t['type']) ?></span></td>
                                    <td class="text-end fw-bold <?= in_array($t['type'], ['deposit','repayment']) ? 'text-success' : 'text-danger' ?>">
                                        <?= in_array($t['type'], ['deposit','repayment']) ? '+' : '-' ?> <?= number_format($t['amount']) ?>
                                    </td>
                                    <td class="text-end text-muted pe-4">KSh <?= number_format($t['running_balance']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="depositModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="process_deposit.php" method="POST" class="modal-content border-0 rounded-4">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold">M-PESA Deposit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Enter the amount to save. You will receive an M-PESA STK prompt on your phone.</p>
                    <div class="form-floating mb-3">
                        <input type="number" name="amount" class="form-control" id="amt" placeholder="100" min="1" required>
                        <label for="amt">Amount (KSh)</label>
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold">Send STK Push</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>