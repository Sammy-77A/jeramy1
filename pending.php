<?php
require 'config.php';

if (!isset($_SESSION['pending_token'])) {
    header('Location: signup.php');
    exit;
}
$token = $_SESSION['pending_token'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Jeramoyie – Payment Pending</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .spinner {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>

<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container text-center">
        <div class="card mx-auto" style="max-width:420px;">
            <div class="card-body p-5" id="statusCard">
                <div class="mb-4">
                    <div class="spinner spinner-border text-success" role="status"></div>
                </div>
                <h4 class="text-success">Payment Requested</h4>
                <p class="text-muted">
                    An M-Pesa STK push has been sent to your phone.<br>
                    <strong>Complete the payment of Ksh 1</strong> to finish registration.
                </p>
                <small class="text-muted">You will be redirected automatically.</small>
            </div>
        </div>
    </div>

    <script>
        const token = <?= json_encode($token) ?>;
        let hasFailed = false;

        const interval = setInterval(() => {
            fetch('check_payment.php?token=' + token)
                .then(r => r.json())
                .then(data => {
                    if (data.paid === true) {
                        clearInterval(interval);
                        window.location.href = 'profile.php';
                    } else if (!data.pending_exists && !data.paid && !hasFailed) {
                        hasFailed = true;
                        clearInterval(interval);
                        document.getElementById('statusCard').innerHTML = `
                            <h4 class="text-danger">Payment Failed or Cancelled</h4>
                            <p class="text-muted">
                                The transaction did not complete.<br>
                                Your registration has been cancelled.
                            </p>
                            <a href="signup.php" class="btn btn-success">Register Again</a>
                        `;
                    }
                })
                .catch(() => {
                    if (!hasFailed) {
                        hasFailed = true;
                        clearInterval(interval);
                        document.getElementById('statusCard').innerHTML = `
                            <h4 class="text-danger">Connection Error</h4>
                            <p class="text-muted">Please try again later.</p>
                            <a href="signup.php" class="btn btn-success">Back to Home</a>
                        `;
                    }
                });
        }, 3000);
    </script>
</body>

</html>