<?php
require 'config.php';

// Security Check: Must be an office bearer (is_admin = 1)
if (!isset($_SESSION['member_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: profile.php');
    exit;
}

$admin_name = $_SESSION['name'];
$admin_role = $_SESSION['role']; 

/**
 * OFFICIALS PERMISSION MATRIX
 * Expanded to include 'sharing' (Dividends), 'penalties' (10% Engine), and 'community' modules.
 */
$permissions = [
    'Chairperson'           => ['members', 'loans', 'attendance', 'finance', 'reports', 'welfare', 'sharing', 'penalties', 'community'],
    'Assistant Chairperson' => ['members', 'loans', 'attendance', 'finance', 'welfare'],
    'Treasury'              => ['finance', 'reports', 'welfare', 'sharing', 'penalties', 'community'],
    'Secretary'             => ['members', 'attendance', 'reports'],
    'Assistant Secretary'   => ['members', 'attendance'],
    'Organizer'             => ['attendance'],
    'Loan Chairperson'      => ['loans', 'finance', 'welfare', 'sharing', 'penalties', 'community'],
    'Trustee'               => ['reports', 'finance', 'sharing'],
    'dev'                   => ['members', 'loans', 'attendance', 'finance', 'reports', 'settings', 'welfare', 'sharing', 'penalties', 'community']
];

function can_access($module, $current_role, $perms) {
    if (!isset($perms[$current_role])) return false;
    return in_array($module, $perms[$current_role]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Console - Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #1a5928; --bg: #f8f9fa; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; }
        .admin-card { 
            background: white; border-radius: 20px; border: none; 
            transition: all 0.3s ease; text-decoration: none; color: inherit;
            display: flex; flex-direction: column; align-items: center; padding: 1.5rem;
            text-align: center; height: 100%;
        }
        .admin-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .icon-box { 
            width: 55px; height: 55px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.6rem; margin-bottom: 1rem;
        }
        .role-badge { background: var(--primary); color: white; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold mb-0 text-dark">Official Console</h1>
            <p class="text-muted small">Bearer: <strong><?= htmlspecialchars($admin_name) ?></strong> <span class="role-badge ms-2"><?= $admin_role ?></span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.html" class="btn btn-outline-secondary rounded-pill px-3 btn-sm" title="Home"><i class="bi bi-house me-1"></i>Home</a>
            <a href="profile.php" class="btn btn-outline-dark rounded-pill px-3 btn-sm"><i class="bi bi-person me-1"></i>Profile</a>
            <a href="community/dashboard.php" class="btn btn-outline-success rounded-pill px-3 btn-sm"><i class="bi bi-people me-1"></i>Community</a>
        </div>
    </div>

    <div class="row g-4">
        <?php if (can_access('finance', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="admin_finance.php" class="admin-card shadow-sm">
                <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-currency-exchange"></i></div>
                <h6 class="fw-bold">Financial Books</h6>
                <p class="text-muted small">Savings & Welfare Pools</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('sharing', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="admin_sharing.php" class="admin-card shadow-sm">
                <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-pie-chart-fill"></i></div>
                <h6 class="fw-bold">Sharing Out</h6>
                <p class="text-muted small">Quarterly & Yearly Dividends</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('loans', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="admin_loans.php" class="admin-card shadow-sm">
                <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-safe2-fill"></i></div>
                <h6 class="fw-bold">Loan Approvals</h6>
                <p class="text-muted small">Process Principal Requests</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('welfare', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="admin_welfare.php" class="admin-card shadow-sm">
                <div class="icon-box bg-danger bg-opacity-10 text-danger"><i class="bi bi-heart-pulse-fill"></i></div>
                <h6 class="fw-bold">Welfare Vetting</h6>
                <p class="text-muted small">Uwezo Loan Vetting</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (in_array($admin_role, ['Chairperson', 'Assistant Chairperson', 'Treasury', 'dev'])): ?>
        <div class="col-md-4 col-lg-3">
            <a href="admin_audit_log.php" class="admin-card shadow-sm">
                <div class="icon-box bg-dark bg-opacity-10 text-dark"><i class="bi bi-shield-check"></i></div>
                <h6 class="fw-bold">Audit Trail</h6>
                <p class="text-muted small">Admin Activity Logs</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('attendance', $admin_role, $permissions) || in_array($admin_role, ['Chairperson', 'Secretary', 'Assistant Secretary', 'dev'])): ?>
        <div class="col-md-4 col-lg-3">
            <a href="admin_announcements.php" class="admin-card shadow-sm">
                <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-megaphone"></i></div>
                <h6 class="fw-bold">Announcements</h6>
                <p class="text-muted small">Broadcast to Members</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('attendance', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="admin_attendance.php" class="admin-card shadow-sm">
                <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-check"></i></div>
                <h6 class="fw-bold">Meeting Logs</h6>
                <p class="text-muted small">Roll Call & Minutes</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('members', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="admin_members.php" class="admin-card shadow-sm">
                <div class="icon-box bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-people-fill"></i></div>
                <h6 class="fw-bold">Member Registry</h6>
                <p class="text-muted small">Manage Roles & Profiles</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('community', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="community/admin/community-dashboard.php" class="admin-card shadow-sm">
                <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-globe2"></i></div>
                <h6 class="fw-bold">Community Portal</h6>
                <p class="text-muted small">Manage Okoa Customers</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('community', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="community/admin/community-customers.php" class="admin-card shadow-sm">
                <div class="icon-box" style="background:rgba(13,148,136,0.1);color:#0d9488"><i class="bi bi-person-badge"></i></div>
                <h6 class="fw-bold">Community Members</h6>
                <p class="text-muted small">View & Manage Accounts</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('community', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="community/admin/community-loans.php" class="admin-card shadow-sm">
                <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-bank2"></i></div>
                <h6 class="fw-bold">Community Loans</h6>
                <p class="text-muted small">Approve & Track Loans</p>
            </a>
        </div>
        <?php endif; ?>

        <?php if (can_access('community', $admin_role, $permissions)): ?>
        <div class="col-md-4 col-lg-3">
            <a href="community/admin/community-reports.php" class="admin-card shadow-sm">
                <div class="icon-box" style="background:rgba(124,58,237,0.1);color:#7c3aed"><i class="bi bi-graph-up"></i></div>
                <h6 class="fw-bold">Community Reports</h6>
                <p class="text-muted small">Financial Analytics</p>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>