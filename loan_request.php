<?php
// loan_request.php
require 'config.php';

// --- 1. Security Check ---
// Ensure the session matches your members table 'paid' column logic
if (!isset($_SESSION['member_id']) || !isset($_SESSION['paid']) || $_SESSION['paid'] != 1) {
    header('Location: login.php');
    exit;
}

$member_id = $_SESSION['member_id'];
$error = '';
$success = '';

try {
    // --- 2. Fetch borrowing limits from the member_financial_summary View ---
    $stmt = $pdo->prepare("SELECT normal_savings_balance, table_banking_balance FROM member_financial_summary WHERE id = ?");
    $stmt->execute([$member_id]);
    $fin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Rule: Normal loan (2x your savings)
    $max_normal_loan = ($fin['normal_savings_balance'] ?? 0) * 2;

    // --- 3. Process Form Submission ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $loan_type = $_POST['loan_type'];
        $amount = floatval($_POST['amount']);
        $purpose = $_POST['purpose'];
        $request_date = date('Y-m-d H:i:s');

        // Working Plan Validations
        if ($loan_type == 'normal') {
            if ($amount > $max_normal_loan) {
                $error = "Limit Exceeded: Your maximum Normal Loan is " . number_format($max_normal_loan, 2) . " based on 2x your savings.";
            } elseif ($amount > 500000) {
                $error = "Limit Exceeded: Normal loans cannot exceed KSh 500,000.";
            }
        } elseif ($loan_type == 'uwezo') {
            // Rule: Uwezo loan tiers (5k or 10k)
            if (!in_array($amount, [5000, 10000])) {
                $error = "Uwezo loans must be either KSh 5,000 or KSh 10,000 as per group policy.";
            }
        }

        if (empty($error)) {
            // Logic tailored to your jeramoyie1.sql table structures
            if ($loan_type == 'normal') {
                $table = 'normal_loans';
                $due_date = date('Y-m-d', strtotime('+1 year')); 
                
                $sql = "INSERT INTO $table (member_id, amount, purpose, request_date, status, due_date, balance) 
                        VALUES (?, ?, ?, ?, 'pending', ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$member_id, $amount, $purpose, $request_date, $due_date, $amount]);

            } elseif ($loan_type == 'uwezo') {
                $table = 'uwezo_loans';
                $due_date = date('Y-m-d', strtotime('+3 months'));
                // Matches your DB column 'welfare_contribution_required'
                $welfare_fee = ($amount == 5000) ? 1000 : 2000;

                $sql = "INSERT INTO $table (member_id, amount, welfare_contribution_required, purpose, request_date, status, due_date, balance) 
                        VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$member_id, $amount, $welfare_fee, $purpose, $request_date, $due_date, $amount]);

            } else {
                $table = 'table_banking_loans';
                $due_date = date('Y-m-d', strtotime('+3 months')); 
                
                $sql = "INSERT INTO $table (member_id, amount, purpose, request_date, status, due_date, balance) 
                        VALUES (?, ?, ?, ?, 'pending', ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$member_id, $amount, $purpose, $request_date, $due_date, $amount]);
            }
            
            $success = "Your " . ucfirst(str_replace('_', ' ', $loan_type)) . " application has been submitted successfully.";
        }
    }
} catch (PDOException $e) {
    $error = "System Error: Unable to process request at this time.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Request • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="profile.php">
                <i class="bi bi-chevron-left me-2"></i>Apply for Loan
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="profile.php" class="btn btn-sm btn-outline-secondary rounded-pill">Dashboard</a>
                <a href="loan_statement.php" class="btn btn-sm btn-outline-success rounded-pill">Loan Statement</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bold mb-3">Loan Application</h4>
                        <p class="text-muted small mb-4">
                            Your current maximum Normal Loan limit based on your savings is
                            <strong>KSh <?= number_format($max_normal_loan, 2) ?></strong>.
                        </p>

                        <form method="post" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Loan Type</label>
                                <select name="loan_type" class="form-select" required>
                                    <option value="normal">Normal Loan</option>
                                    <option value="table_banking">Table Banking Loan</option>
                                    <option value="uwezo">Uwezo Loan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Amount (KSh)</label>
                                <input type="number" name="amount" min="1" step="1" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Purpose / Description</label>
                                <textarea name="purpose" rows="3" class="form-control" placeholder="Briefly describe the purpose of this loan" required></textarea>
                            </div>

                            <div class="col-12 mt-3">
                                <h6 class="fw-bold mb-2">Key Terms</h6>
                                <ul class="small text-muted mb-3">
                                    <li>Normal loans: Maximum 2x your normal savings and capped at KSh 500,000.</li>
                                    <li>Uwezo loans: Allowed amounts are KSh 5,000 or KSh 10,000 only.</li>
                                    <li>Table banking loans: Subject to group review and approvals.</li>
                                    <li>Late repayments may attract penalties as per group policy.</li>
                                </ul>
                            </div>

                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="bi bi-arrow-left me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-main rounded-pill px-4">
                                    <i class="bi bi-send-check me-2"></i>Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>