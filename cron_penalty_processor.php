<?php
require 'config.php';

// 1. Process Uwezo Penalties (10% after 3 months)
$pdo->query("UPDATE uwezo_loans 
             SET penalty_accrued = amount * 0.10 
             WHERE status = 'active' 
             AND request_date <= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) 
             AND penalty_accrued = 0");

// 2. Process Small Normal Loans (10% after 3 months if < 30k)
$pdo->query("UPDATE normal_loans 
             SET penalty_accrued = amount * 0.10 
             WHERE status = 'active' 
             AND amount < 30000 
             AND request_date <= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) 
             AND penalty_accrued = 0");

// 3. Process Large Normal Loans (10% after 1 year if > 30k)
$pdo->query("UPDATE normal_loans 
             SET penalty_accrued = amount * 0.10 
             WHERE status = 'active' 
             AND amount >= 30000 
             AND request_date <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) 
             AND penalty_accrued = 0");

// Audit Trail: Log penalty engine execution
log_admin_activity($pdo, "Penalty Engine Run", "Automated penalty processor executed");
?>