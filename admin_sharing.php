<?php
require 'config.php';

// Security: Treasury, Chairperson, or dev only
$allowed = ['Treasury', 'Chairperson', 'dev'];
if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], $allowed)) {
    header('Location: profile.php'); exit;
}

// --- POOLED MONEY CALCULATIONS ---

// 1. Normal/Weekly Pool (Yearly)
$total_weekly = $pdo->query("SELECT SUM(total_weekly_paid) FROM member_financial_summary")->fetchColumn() ?: 0;
$total_normal_savings = $pdo->query("SELECT SUM(normal_savings_balance) FROM member_financial_summary")->fetchColumn() ?: 0;
$total_penalties = $pdo->query("SELECT SUM(penalty_accrued) FROM normal_loans")->fetchColumn() + 
                   $pdo->query("SELECT SUM(penalty_accrued) FROM uwezo_loans")->fetchColumn();
$yearly_distribution_pool = $total_penalties; // Dividends come from penalties/interest profit

// 2. Table Banking Pool (Quarterly)
$total_table_shares = $pdo->query("SELECT SUM(table_banking_balance) FROM member_financial_summary")->fetchColumn() ?: 0;
$table_interest_pool = $pdo->query("SELECT SUM(interest_accrued) FROM table_banking_loans")->fetchColumn() ?: 0;

// 3. Global Stats for Ratio
$total_weight = $pdo->query("SELECT SUM(total_dividend_weight) FROM member_financial_summary")->fetchColumn() ?: 1;
$est_yearly_rate = ($yearly_distribution_pool / $total_weight) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sharing Out Hub • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --jera-green: #1a5928; --bg: #f4f7f6; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; padding-top: 30px; }
        .stat-card { background: white; border-radius: 15px; border: none; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .pool-highlight { border-left: 5px solid var(--jera-green); }
        .btn-execute { background: var(--jera-green); color: white; border-radius: 50px; font-weight: 600; transition: 0.3s; }
        .btn-execute:hover { background: #13421e; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Sharing Out Hub</h2>
            <p class="text-muted">Finalizing Quarterly and Yearly Profit Distribution</p>
        </div>
        <div class="d-flex gap-2">
            <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-pill px-4"><i class="bi bi-chevron-left me-1"></i>Console</a>
            <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-4">Profile</a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card pool-highlight">
                <small class="text-uppercase fw-bold text-muted">Weekly Pool (Liquidity)</small>
                <h3 class="fw-bold mt-1">KSh <?= number_format($total_weekly, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card pool-highlight">
                <small class="text-uppercase fw-bold text-muted">Normal Savings Pool</small>
                <h3 class="fw-bold mt-1">KSh <?= number_format($total_normal_savings, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card pool-highlight">
                <small class="text-uppercase fw-bold text-muted">Table Banking Shares</small>
                <h3 class="fw-bold mt-1">KSh <?= number_format($total_table_shares, 2) ?></h3>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card stat-card p-4 h-100 border-0 shadow">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h5 class="fw-bold">Normal & Weekly Share-Out<br><small class="text-muted fw-normal">Annual Cycle</small></h5>
                    <i class="bi bi-calendar-event fs-3 text-success"></i>
                </div>
                
                <div class="bg-light p-3 rounded-4 mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pooled Profit (Penalties)</span>
                        <span class="fw-bold">KSh <?= number_format($yearly_distribution_pool, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Projected Dividend Rate</span>
                        <span class="badge bg-success"><?= number_format($est_yearly_rate, 2) ?>%</span>
                    </div>
                </div>

                <p class="small text-muted mb-4">This will distribute profits across all members based on their combined Weekly and Normal Savings ratio. Payments will be triggered according to each member's set preference (M-PESA, Cash, or Re-investment).</p>
                
                <a href="sharing_normal_process.php" class="btn btn-execute w-100 py-3 mt-auto">
                    Execute Yearly Share-Out
                </a>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card stat-card p-4 h-100 border-0 shadow">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h5 class="fw-bold">Table Banking Share-Out<br><small class="text-muted fw-normal">3-Month Cycle</small></h5>
                    <i class="bi bi-clock-history fs-3 text-primary"></i>
                </div>

                <div class="bg-light p-3 rounded-4 mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pooled Table Interest</span>
                        <span class="fw-bold">KSh <?= number_format($table_interest_pool, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Current Cycle Liquidity</span>
                        <span class="fw-bold">KSh <?= number_format($total_table_shares, 2) ?></span>
                    </div>
                </div>

                <p class="small text-muted mb-4">Distributed every three months. Calculates profit per share based on interest accrued from table banking loans within the current quarter.</p>
                
                <a href="sharing_table_process.php" class="btn btn-outline-success border-2 rounded-pill w-100 py-3 mt-auto fw-bold">
                    Execute Quarterly Table Share-Out
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>