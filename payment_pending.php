<?php
require 'config.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: login.php');
    exit;
}

$checkout_id = $_SESSION['pending_checkout_id'] ?? $_GET['checkout_id'] ?? null;
$payment_type = $_SESSION['pending_payment_type'] ?? $_GET['type'] ?? 'payment';
$payment_amount = $_SESSION['pending_payment_amount'] ?? 0;

if (!$checkout_id) {
    header('Location: profile.php');
    exit;
}

$type_labels = [
    'weekly' => 'Weekly Contribution',
    'normal_savings' => 'Normal Savings Deposit',
    'table_banking' => 'Table Banking Shares',
    'welfare' => 'Welfare Contribution',
    'loan_repayment' => 'Loan Repayment',
    'deposit' => 'Savings Deposit',
    'payment' => 'Payment',
];
$label = $type_labels[$payment_type] ?? ucfirst(str_replace('_', ' ', $payment_type));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Pending - Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem; }
        .progress-ring { width:80px; height:80px; border:5px solid #e9ecef; border-top-color:var(--primary); border-radius:50%; animation:spin 1s linear infinite; }
        @keyframes spin { to { transform:rotate(360deg); } }
        .pulse-text { animation:pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
        .timer-bar { height:4px; background:#e9ecef; border-radius:2px; overflow:hidden; margin-top:1.5rem; }
        .timer-fill { height:100%; background:var(--primary); transition:width 1s linear; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top bg-white border-bottom shadow-sm py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-success fw-bold" href="profile.php">
                <i class="bi bi-chevron-left me-2"></i>Dashboard
            </a>
        </div>
    </nav>
    <div class="pending-card" style="max-width:460px;width:100%; margin-top: 4rem;">
        <div id="statusCard">
            <div class="mb-4 d-flex justify-content-center">
                <div class="progress-ring"></div>
            </div>
            <h4 class="fw-bold" style="color:var(--primary)">
                <i class="bi bi-phone-vibrate me-2"></i>Check Your Phone
            </h4>
            <p class="text-muted mt-3">
                An M-PESA STK push for <strong>KSh <?= number_format($payment_amount, 2) ?></strong>
                has been sent to your phone.<br>
                <span class="small">Category: <strong><?= htmlspecialchars($label) ?></strong></span>
            </p>
            <div class="pulse-text mt-3">
                <small class="text-muted">
                    <i class="bi bi-arrow-repeat me-1"></i>Waiting for M-PESA confirmation...
                </small>
            </div>
            <div class="timer-bar">
                <div class="timer-fill" id="timerFill" style="width:0%"></div>
            </div>
            <small class="text-muted mt-2 d-block" id="timerText">Checking... (0s)</small>
        </div>
    </div>

    <script>
        var checkoutId = <?= json_encode($checkout_id) ?>;
        var payLabel = <?= json_encode($label) ?>;
        var payAmount = <?= json_encode(number_format($payment_amount, 2)) ?>;
        var elapsed = 0;
        var maxWait = 120;
        var hasResolved = false;
        var stkQueryTriggeredAt = 0;
        var STK_QUERY_DELAY = 15;

        function showSuccess() {
            document.getElementById('statusCard').innerHTML =
                '<div class="mb-3"><i class="bi bi-check-circle-fill text-success" style="font-size:3.5rem"></i></div>' +
                '<h4 class="fw-bold text-success">Payment Confirmed!</h4>' +
                '<p class="text-muted">Your ' + payLabel + ' of KSh ' + payAmount + ' has been processed successfully.</p>' +
                '<a href="profile.php" class="btn btn-main rounded-pill px-4 mt-2"><i class="bi bi-house me-2"></i>Back to Dashboard</a>';
        }
        function showFailed(resultDesc) {
            document.getElementById('statusCard').innerHTML =
                '<div class="mb-3"><i class="bi bi-x-circle-fill text-danger" style="font-size:3.5rem"></i></div>' +
                '<h4 class="fw-bold text-danger">Payment Failed</h4>' +
                '<p class="text-muted">' + (resultDesc || 'The transaction was cancelled or failed.') + '</p>' +
                '<a href="profile.php" class="btn btn-main rounded-pill px-4 mt-2"><i class="bi bi-arrow-clockwise me-2"></i>Try Again</a>';
        }
        function showTimeout() {
            document.getElementById('statusCard').innerHTML =
                '<div class="mb-3"><i class="bi bi-clock-history text-warning" style="font-size:3.5rem"></i></div>' +
                '<h4 class="fw-bold text-warning">Taking Too Long</h4>' +
                '<p class="text-muted">We have not received a confirmation yet. The payment may still process. Check your M-PESA messages.</p>' +
                '<a href="profile.php" class="btn btn-main rounded-pill px-4 mt-2"><i class="bi bi-house me-2"></i>Back to Dashboard</a>';
        }
        function resolve(completed, resultDesc) {
            if (hasResolved) return;
            hasResolved = true;
            clearInterval(pollInterval);
            clearInterval(timerInterval);
            if (completed) showSuccess(); else showFailed(resultDesc);
        }

        function updateTimer() {
            elapsed++;
            var pct = Math.min((elapsed / maxWait) * 100, 100);
            document.getElementById('timerFill').style.width = pct + '%';
            document.getElementById('timerText').textContent = 'Checking... (' + elapsed + 's)';
        }
        var timerInterval = setInterval(updateTimer, 1000);

        var pollInterval = setInterval(function() {
            // 1) Poll our DB (callback may have updated it)
            fetch('check_status.php?id=' + encodeURIComponent(checkoutId))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.status === 'completed') {
                        resolve(true);
                    } else if (data.status === 'failed') {
                        resolve(false, data.result_desc);
                    }
                })
                .catch(function() {});

            // 2) After 15s, trigger STK Query fallback so we detect completion even if callback never arrives
            if (elapsed >= STK_QUERY_DELAY && (stkQueryTriggeredAt === 0 || elapsed - stkQueryTriggeredAt >= 12)) {
                stkQueryTriggeredAt = elapsed;
                fetch('stk_query_fallback.php?id=' + encodeURIComponent(checkoutId))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.status === 'completed') {
                            resolve(true);
                        } else if (data.status === 'failed') {
                            resolve(false, data.result_desc);
                        }
                    })
                    .catch(function() {});
            }

            if (elapsed >= maxWait && !hasResolved) {
                hasResolved = true;
                clearInterval(pollInterval);
                clearInterval(timerInterval);
                showTimeout();
            }
        }, 3000);
    </script>
</body>
</html>
