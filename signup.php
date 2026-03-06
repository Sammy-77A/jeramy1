<?php
require 'config.php';

// Redirect if already logged in
if (isset($_SESSION['member_id'])) {
  header('Location: profile.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Join Jera Moyie • Create Account</title>
  <link rel="icon" type="image/png" href="favittl.ico">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <style>
    :root {
      --primary: #2c7a4b;
      --primary-hover: #1e5a38;
      --accent: #f59e0b;
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
      padding: 2rem 1rem;
    }

    .home-btn {
      position: absolute;
      top: 20px;
      left: 20px;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: white;
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      z-index: 10;
    }

    .home-btn:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-2px);
    }

    .signup-card {
      background: white;
      border: none;
      border-radius: 24px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.05);
      width: 100%;
      max-width: 500px;
      padding: 2.5rem;
      position: relative;
      overflow: hidden;
    }

    .signup-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background: linear-gradient(90deg, var(--primary), var(--accent));
    }

    .logo-img {
      width: 70px;
      height: 70px;
      object-fit: contain;
      border-radius: 50%;
      padding: 4px;
      border: 2px solid #f0f0f0;
      margin-bottom: 1rem;
    }

    h2 {
      font-family: 'Playfair Display', serif;
      color: #333;
      font-weight: 700;
    }

    /* Floating Inputs */
    .form-floating>.form-control {
      border: 1px solid #e0e0e0;
      border-radius: 12px;
    }

    .form-floating>.form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(44, 122, 75, 0.1);
    }

    .form-floating>label {
      color: #999;
    }

    /* Password Toggles */
    .password-toggle {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
      z-index: 5;
    }

    /* Submit Button */
    .btn-submit {
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

    .btn-submit:hover {
      background: var(--primary-hover);
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(44, 122, 75, 0.15);
    }

    /* Fee Badge */
    .fee-badge {
      background: #fff8e1;
      color: #b45309;
      border: 1px solid #fcd34d;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 1.5rem;
    }

    .spinner-border {
      width: 1.2rem;
      height: 1.2rem;
      border-width: 2px;
      display: none;
    }
  </style>
</head>

<body>

  <a href="index.html" class="home-btn" title="Back to Home">
    <i class="bi bi-arrow-left"></i>
  </a>

  <div class="signup-card" data-aos="fade-up" data-aos-duration="800">

    <div class="text-center">
      <img src="images/jeramoyie_snp.png" alt="Logo" class="logo-img" onerror="this.src='https://ui-avatars.com/api/?name=JM&background=2c7a4b&color=fff&size=70'">
      <h2>Join Jera Moyie</h2>

      <div class="fee-badge">
        <i class="bi bi-info-circle-fill"></i>
        <span>Registration Fee: <strong>KSh 1</strong></span>
      </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-info small text-center rounded-3 border-0 bg-info-subtle text-info-emphasis mb-4">
        <?= htmlspecialchars($_GET['msg']) ?>
      </div>
    <?php endif; ?>

    <form id="registerForm" action="register.php" method="POST" class="needs-validation" novalidate>

      <div class="form-floating mb-3">
        <input type="text" class="form-control" name="name" id="name" placeholder="John Doe" required>
        <label for="name">Full Name</label>
        <div class="invalid-feedback">Please enter your full name.</div>
      </div>

      <div class="form-floating mb-3">
        <input type="number" class="form-control" name="national_id" id="national_id" placeholder="12345678" required>
        <label for="national_id">National ID</label>
      </div>

      <div class="form-floating mb-3">
        <input type="tel" class="form-control" name="phone" id="phoneInput" placeholder="07..." required>
        <label for="phoneInput">Phone Number (Safaricom)</label>
        <div class="invalid-feedback">Please enter a valid Kenyan number (07... or 011...).</div>
      </div>

      <div class="row g-2 mb-4">
        <div class="col-md-6">
          <div class="form-floating position-relative">
            <input type="password" class="form-control" name="password" id="password" placeholder="Pass" minlength="8" required>
            <label for="password">Password</label>
            <i class="bi bi-eye-slash password-toggle" onclick="togglePass('password', this)"></i>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating position-relative">
            <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Confirm" required>
            <label for="password_confirm">Confirm</label>
            <i class="bi bi-eye-slash password-toggle" onclick="togglePass('password_confirm', this)"></i>
            <div class="invalid-feedback">Passwords do not match.</div>
          </div>
        </div>
      </div>

      <button type="submit" id="submitBtn" class="btn btn-submit">
        <span>Create Account</span>
        <div class="spinner-border text-white" role="status"></div>
      </button>
    </form>

    <div class="text-center mt-4">
      <p class="small text-muted">
        Already a member? <a href="login.php" class="text-decoration-none fw-bold text-success">Sign In</a>
      </p>
    </div>

    <div class="text-center mt-2">
      <small class="text-muted" style="font-size: 0.7rem;">
        <i class="bi bi-shield-lock-fill me-1 text-success"></i> Payments secured by M-PESA
      </small>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();

    // Toggle Password Visibility
    function togglePass(inputId, icon) {
      const input = document.getElementById(inputId);
      const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
      input.setAttribute('type', type);
      icon.classList.toggle('bi-eye');
      icon.classList.toggle('bi-eye-slash');
    }

    // Live Validation Logic
    const form = document.getElementById('registerForm');
    const phoneInput = document.getElementById('phoneInput');
    const pass1 = document.getElementById('password');
    const pass2 = document.getElementById('password_confirm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('span');
    const spinner = submitBtn.querySelector('.spinner-border');

    // Phone Validation (Kenya specific)
    phoneInput.addEventListener('input', function() {
      let val = this.value.replace(/\D/g, ''); // Remove non-digits
      if (val.startsWith('254')) val = '0' + val.substring(3);

      // Check strictly for 07xx or 011x
      const isValid = /^0(7|1)\d{8}$/.test(val);

      if (isValid) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        this.setCustomValidity('');
      } else {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
        this.setCustomValidity('Invalid phone');
      }
    });

    // Password Match Check
    function checkPasswords() {
      if (pass2.value && pass1.value !== pass2.value) {
        pass2.classList.add('is-invalid');
        pass2.setCustomValidity('Mismatch');
      } else {
        pass2.classList.remove('is-invalid');
        pass2.setCustomValidity('');
      }
    }
    pass1.addEventListener('input', checkPasswords);
    pass2.addEventListener('input', checkPasswords);

    // Form Submit
    form.addEventListener('submit', function(e) {
      checkPasswords(); // Final check

      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      } else {
        // Show Loading
        submitBtn.classList.add('disabled');
        btnText.textContent = 'Processing Payment...';
        spinner.style.display = 'block';
      }
      form.classList.add('was-validated');
    });
  </script>
</body>

</html>