<?php
header('Content-Type: application/json');
require 'config.php';

$mpesaResponse = file_get_contents('php://input');
$data = json_decode($mpesaResponse, true);

// Log callback
$log = fopen('M_PESAConfirmationResponse.txt', 'a');
fwrite($log, "[" . date('Y-m-d H:i:s') . "] " . $mpesaResponse . PHP_EOL);
fclose($log);

$response = ["ResultCode" => 1, "ResultDesc" => "Failed"];
$token = $_GET['token'] ?? null;
$resultCode = $data['Body']['stkCallback']['ResultCode'] ?? null;

// Extract actual M-PESA receipt number from callback metadata
$mpesaReceiptNumber = null;
if ($resultCode == 0) {
  $callbackItems = $data['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];
  foreach ($callbackItems as $item) {
    if ($item['Name'] === 'MpesaReceiptNumber') {
      $mpesaReceiptNumber = $item['Value'];
      break;
    }
  }
}

if ($resultCode == 0 && $token) {
  $handled = false;
  // Use real M-PESA receipt as reference, fallback to token prefix if missing
  $ref = $mpesaReceiptNumber ?: ('TXN-' . strtoupper(substr($token, 0, 10)));

  // --- 1. Check MAIN system pending registrations ---
  $stmt = $pdo->prepare("SELECT * FROM pending_registrations WHERE token = ?");
  $stmt->execute([$token]);
  if ($pending = $stmt->fetch()) {
    $stmt = $pdo->prepare(
      "INSERT INTO members (name, national_id, phone, password_hash, paid) VALUES (?, ?, ?, ?, 1)"
    );
    $stmt->execute([$pending['name'], $pending['national_id'], $pending['phone'], $pending['password_hash']]);
    $pdo->prepare("DELETE FROM pending_registrations WHERE token = ?")->execute([$token]);
    $response = ["ResultCode" => 0, "ResultDesc" => "Accepted"];
    $handled = true;
  }

  // --- 2. Check COMMUNITY pending registrations ---
  if (!$handled) {
    $stmt = $pdo->prepare("SELECT * FROM community_pending_registrations WHERE token = ?");
    $stmt->execute([$token]);
    if ($cpending = $stmt->fetch()) {
      $stmt = $pdo->prepare(
        "INSERT INTO community_customers (full_name, phone_number, national_id, email, password, status) VALUES (?, ?, ?, ?, ?, 'active')"
      );
      $stmt->execute([$cpending['full_name'], $cpending['phone_number'], $cpending['national_id'], $cpending['email'], $cpending['password_hash']]);
      $pdo->prepare("DELETE FROM community_pending_registrations WHERE token = ?")->execute([$token]);
      $response = ["ResultCode" => 0, "ResultDesc" => "Accepted"];
      $handled = true;
    }
  }

  // --- 3. Check COMMUNITY pending deposits (savings via M-PESA) ---
  if (!$handled) {
    $stmt = $pdo->prepare("SELECT * FROM community_pending_deposits WHERE token = ?");
    $stmt->execute([$token]);
    if ($dep = $stmt->fetch()) {
      require_once __DIR__ . '/community/includes/functions.php';
      $pdo->prepare("INSERT INTO community_savings (customer_id, product_id, amount, transaction_type, reference_code) VALUES (?,?,?,'deposit',?)")
        ->execute([$dep['customer_id'], $dep['product_id'], $dep['amount'], $ref]);

      $pdo->prepare("INSERT INTO community_security_log (event_type, customer_id, details) VALUES ('deposit_confirmed', ?, ?)")
        ->execute([$dep['customer_id'], "Amount: {$dep['amount']}, Ref: $ref, Token: $token"]);

      $pdo->prepare("DELETE FROM community_pending_deposits WHERE token = ?")->execute([$token]);
      $response = ["ResultCode" => 0, "ResultDesc" => "Accepted"];
      $handled = true;
    }
  }

  // --- 4. Check COMMUNITY pending repayments (loan repayment via M-PESA) ---
  if (!$handled) {
    $stmt = $pdo->prepare("SELECT * FROM community_pending_repayments WHERE token = ?");
    $stmt->execute([$token]);
    if ($rep = $stmt->fetch()) {
      require_once __DIR__ . '/community/includes/functions.php';
      $pdo->prepare("INSERT INTO community_loan_repayments (loan_id, amount, payment_method, reference_code) VALUES (?,?,'mpesa',?)")
        ->execute([$rep['loan_id'], $rep['amount'], $ref]);

      // Check if loan is now fully paid
      $loanStmt = $pdo->prepare("SELECT total_repayable, (SELECT COALESCE(SUM(amount),0) FROM community_loan_repayments WHERE loan_id = ?) as total_paid FROM community_loans WHERE id = ?");
      $loanStmt->execute([$rep['loan_id'], $rep['loan_id']]);
      $loanData = $loanStmt->fetch();
      if ($loanData && $loanData['total_paid'] >= $loanData['total_repayable']) {
        $pdo->prepare("UPDATE community_loans SET status = 'completed' WHERE id = ?")->execute([$rep['loan_id']]);
      }

      $pdo->prepare("INSERT INTO community_security_log (event_type, customer_id, details) VALUES ('repayment_confirmed', ?, ?)")
        ->execute([$rep['customer_id'], "Loan: {$rep['loan_id']}, Amount: {$rep['amount']}, Ref: $ref, Token: $token"]);

      $pdo->prepare("DELETE FROM community_pending_repayments WHERE token = ?")->execute([$token]);
      $response = ["ResultCode" => 0, "ResultDesc" => "Accepted"];
      $handled = true;
    }
  }
}
else if ($token) {
  // Failure – clean up ALL pending tables
  $reason = $data['Body']['stkCallback']['ResultDesc'] ?? 'Unknown failure';
  $log = fopen('failed_payments.log', 'a');
  fwrite($log, "[" . date('Y-m-d H:i:s') . "] Token $token – $reason\n");
  fclose($log);

  $pdo->prepare("DELETE FROM pending_registrations WHERE token = ?")->execute([$token]);
  $pdo->prepare("DELETE FROM community_pending_registrations WHERE token = ?")->execute([$token]);
  $pdo->prepare("DELETE FROM community_pending_deposits WHERE token = ?")->execute([$token]);
  $pdo->prepare("DELETE FROM community_pending_repayments WHERE token = ?")->execute([$token]);
}

@unlink('temp_token.txt');

echo json_encode($response);
exit;
