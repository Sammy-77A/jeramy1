<?php
if (!isset($_SESSION['reset_req_id'])) {
    header("Location: forgot-password.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifying... • Jeramoyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f3f4f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: sans-serif;
        }

        .card {
            max-width: 400px;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="card p-5 text-center">
        <h4 class="mb-3">Check your Phone</h4>
        <p class="text-muted">We sent a request to <strong><?= $_SESSION['reset_phone'] ?></strong>.</p>
        <p class="small">Enter your M-Pesa PIN to prove it's you.</p>
        <div class="spinner-border text-success mt-3" role="status"></div>
        <p class="mt-3 small text-muted" id="statusMsg">Waiting for confirmation...</p>
    </div>

    <script>
        setInterval(function() {
            fetch('check_reset_status.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = 'reset-password.php';
                    } else if (data.status === 'failed') {
                        alert("Verification Failed. Please try again.");
                        window.location.href = 'forgot-password.php';
                    }
                });
        }, 2000); // Check every 2 seconds
    </script>
</body>

</html>