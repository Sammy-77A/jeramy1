<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Jeramoyie – Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .card {
            max-width: 500px;
        }
    </style>
</head>

<body class="d-flex align-items-center min-vh-100">
    <div class="container text-center">
        <div class="card mx-auto shadow">
            <div class="card-body p-5">
                <div class="mb-4">
                    <svg width="64" height="64" fill="#dc3545" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                        <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z" />
                    </svg>
                </div>
                <h4 class="text-danger">Oops! Something went wrong</h4>
                <p class="text-muted">
                    <?= htmlspecialchars($_GET['msg'] ?? 'An unknown error occurred.') ?>
                </p>
                <a href="signup.php" class="btn btn-success mt-3">Back to Registration</a>
            </div>
        </div>
    </div>
</body>

</html>