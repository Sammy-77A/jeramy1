<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawPhone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $token    = $_POST['csrf'] ?? '';

    // Normalize phone number (07xx -> 2547xx)
    $phone = preg_replace('/^0/', '254', $rawPhone);

    if ($token !== ($_SESSION['csrf'] ?? '')) {
        $error = "Session expired. Please refresh.";
    } elseif (!preg_match('/^(07[0-9]{8}|2547[0-9]{8})$/', $rawPhone)) {
        $error = "Invalid phone number format.";
    } else {
        // Fetch all user details including role and native status
        $stmt = $pdo->prepare("SELECT * FROM members WHERE phone = ?");
        $stmt->execute([$phone]);
        $member = $stmt->fetch();

        if ($member && password_verify($password, $member['password_hash'])) {
            // --- 1. SET CORE SESSION DATA ---
            $_SESSION['member_id'] = $member['id'];
            $_SESSION['name']      = $member['name'];
            $_SESSION['paid']      = $member['paid']; 

            // --- 2. SET ADMIN & NATIVE PRIVILEGES ---
            // is_admin (1 or 0) determines if they see the Admin Dashboard button
            $_SESSION['is_admin']  = (int)$member['is_admin']; 
            // role (e.g., 'Loan Chairperson') determines specific powers in admin pages
            $_SESSION['role']      = $member['role'];
            // is_native (1 or 0) determines which portal they access
            $_SESSION['is_native'] = (int)$member['is_native'];

            // --- 3. ROUTING LOGIC ---
            if ($_SESSION['is_native'] === 1) {
                // Native members go to the main Jera Moyie dashboard
                header('Location: profile.php');
            } else {
                // Non-native members are routed to the Community/Okoa portal
                header('Location: community_profile.php');
            }
            exit;
        } else {
            $error = "Incorrect phone number or password.";
        }
    }
}

// Generate CSRF token for security
$_SESSION['csrf'] = bin2hex(random_bytes(16));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login  Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2c7a4b;
            --primary-hover: #1e5a38;
            --accent: #f59e0b;
            --text-dark: #343a40;
            --bg-light: #f3f4f6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            background-image: radial-gradient(at 0% 0%, rgba(44, 122, 75, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(245, 158, 11, 0.05) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: white;
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 50%;
            padding: 5px;
            border: 2px solid #f0f0f0;
            margin-bottom: 1.5rem;
        }

        h2 { font-family: 'Playfair Display', serif; color: var(--text-dark); font-weight: 700; margin-bottom: 0.5rem; }
        .subtitle { color: #6c757d; font-size: 0.95rem; margin-bottom: 2rem; }

        .form-floating>.form-control { border: 1px solid #e0e0e0; border-radius: 12px; }
        .form-floating>.form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(44, 122, 75, 0.1); }
        
        .btn-login {
            background: var(--primary);
            border: none;
            color: white;
            font-weight: 600;
            padding: 1rem;
            border-radius: 12px;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(44, 122, 75, 0.15);
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 5;
        }

        .login-nav {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .login-nav .nav-brand { color: var(--text-dark); font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        .login-nav .nav-brand:hover { color: var(--primary); }
        .login-nav .nav-link-custom { color: var(--primary); font-weight: 500; text-decoration: none; padding: 0.5rem 1rem; border-radius: 9999px; transition: all 0.2s; }
        .login-nav .nav-link-custom:hover { background: rgba(44, 122, 75, 0.1); color: var(--primary-hover); }
        .login-nav .nav-link-custom.btn-reg { background: var(--primary); color: white !important; }
        .login-nav .nav-link-custom.btn-reg:hover { background: var(--primary-hover); color: white !important; }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top login-nav py-3">
        <div class="container">
            <a class="nav-brand" href="index.html"><i class="bi bi-chevron-left me-1"></i> Home</a>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="community/login.php" class="nav-link-custom d-none d-sm-inline-block"><i class="bi bi-people me-1"></i> Community</a>
                <a href="signup.php" class="nav-link-custom btn-reg px-3"><i class="bi bi-person-plus me-1"></i> Create Account</a>
            </div>
        </div>
    </nav>

    <div class="login-card" data-aos="fade-up" style="margin-top: 5rem;">
        <div class="text-center">
            <img src="images/jm_logo.jpg" alt="Logo" class="logo-img" onerror="this.src='https://ui-avatars.com/api/?name=JM&background=2c7a4b&color=fff&size=80'">
            <h2>Welcome Back</h2>
            <p class="subtitle">Securely login to your Jera Moyie account</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger py-2 small rounded-3 border-0 bg-danger-subtle text-danger mb-4">
                <i class="bi bi-exclamation-circle-fill me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

            <div class="form-floating mb-3">
                <input type="tel" class="form-control" id="phoneInput" name="phone" placeholder="07..." required>
                <label for="phoneInput">Phone Number</label>
            </div>

            <div class="form-floating mb-2 position-relative">
                <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Password" required>
                <label for="passwordInput">Password</label>
                <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
            </div>
            
            <div class="d-flex justify-content-end mb-4">
                <a href="forgot-password.php" class="text-decoration-none small text-success fw-bold">Forgot Password?</a>
            </div>

            <button type="submit" id="loginBtn" class="btn btn-login">
                <span>Sign In</span>
                <div class="spinner-border text-white spinner-border-sm" id="loginSpinner" role="status" style="display:none;"></div>
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="small text-muted mb-1">New to Jera Moyie?</p>
            <a href="signup.php" class="text-decoration-none fw-bold text-success">Create an Account</a>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#passwordInput');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        document.getElementById('loginForm').onsubmit = function() {
            document.getElementById('loginSpinner').style.display = 'inline-block';
            document.getElementById('loginBtn').classList.add('disabled');
        };
    </script>
</body>
</html>
