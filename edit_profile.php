<?php
require 'config.php';

// Security Check
if (!isset($_SESSION['member_id']) || !isset($_SESSION['paid']) || $_SESSION['paid'] !== 1) {
    header('Location: login.php');
    exit;
}

$member_id = $_SESSION['member_id'];
$success = $error = '';

// Fetch Member Data
try {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();
} catch (PDOException $e) {
    die("System Error.");
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $update = $pdo->prepare("UPDATE members SET email = ? WHERE id = ?");
        if ($update->execute([$email, $member_id])) {
            $success = "Email address updated successfully!";
            $member['email'] = $email; // Refresh local variable
        } else {
            $error = "Unable to update email. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Details • Jera Moyie</title>
    <link rel="icon" href="images/jm.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2c7a4b;
            --primary-dark: #1e5a38;
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .edit-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 30px;
            text-align: center;
            color: white;
        }

        .card-header-custom h4 {
            font-family: 'Playfair Display', serif;
            margin: 0;
            font-weight: 700;
        }

        .form-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #6c757d;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e0e0e0;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25 row rgba(44, 122, 75, 0.1);
        }

        .form-control[readonly] {
            background-color: #f1f3f5;
            color: #adb5bd;
        }

        .btn-save {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-back {
            border-radius: 12px;
            padding: 10px;
            font-weight: 500;
            color: #6c757d;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 10px;
            transition: color 0.2s;
        }

        .btn-back:hover {
            color: var(--primary);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm border-bottom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-dark" href="profile.php">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </nav>
    <div class="edit-card mx-3" style="margin-top: 1rem;">
        <div class="card-header-custom">
            <h4>Update Profile</h4>
            <p class="mb-0 opacity-75 small">Secure your account with an email</p>
        </div>

        <div class="p-4">
            <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center small py-2">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center small py-2">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($member['name']) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($member['phone']) ?>" readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="e.g. name@example.com" value="<?= htmlspecialchars($member['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-text x-small mt-2">
                        <i class="bi bi-info-circle me-1"></i> Required for password recovery.
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-save">
                        <i class="bi bi-shield-check me-2"></i> Save Changes
                    </button>
                    <a href="profile.php" class="btn-back small">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>

</html>