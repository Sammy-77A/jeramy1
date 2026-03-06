<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Jeramoyie – Registration Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container text-center">
        <div class="card mx-auto" style="max-width:420px;">
            <div class="card-body p-5">
                <h4 class="text-danger">Registration Failed</h4>
                <p class="text-muted">
                    <?= htmlspecialchars($_GET['msg'] ?? 'We could not initiate the M-Pesa payment.') ?>
                </p>
                <a href="signup.php" class="btn btn-success">Try Again</a>
            </div>
        </div>
    </div>
</body>

</html>