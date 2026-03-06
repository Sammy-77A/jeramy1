<?php
require 'config.php';

// Security: Check permissions
$allowed_roles = ['Chairperson', 'Assistant Chairperson', 'Secretary', 'Assistant Secretary', 'dev'];
if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: profile.php');
    exit;
}

// === PDF EXPORT LOGIC ===
if (isset($_GET['export_pdf'])) {
    $pdfPath = __DIR__ . '/dompdf/autoload.inc.php'; 
    if (file_exists($pdfPath)) {
        require_once $pdfPath;
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new \Dompdf\Dompdf($options);
        
        $stmt = $pdo->query("SELECT id, name, phone, national_id, role, is_native, status FROM members ORDER BY name ASC");
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = '
        <style>
            body { font-family: "DejaVu Sans", sans-serif; color: #333; }
            h2 { color: #1a5928; text-align: center; text-transform: uppercase; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background: #1a5928; color: white; padding: 10px; font-size: 11px; text-align: left; }
            td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 10px; }
        </style>
        <h2>Jera Moyie Member Register</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>ID Number</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($members as $m) {
            $formattedPhone = preg_replace('/^254/', '0', $m['phone']);
            $typeText = $m['is_native'] ? 'NATIVE' : 'COMMUNITY';
            $html .= "<tr>
                <td>" . str_pad($m['id'], 3, '0', STR_PAD_LEFT) . "</td>
                <td><strong>" . htmlspecialchars($m['name']) . "</strong></td>
                <td>" . htmlspecialchars($m['national_id']) . "</td>
                <td>" . $formattedPhone . "</td>
                <td>" . htmlspecialchars($m['role']) . "</td>
                <td>{$typeText}</td>
            </tr>";
        }
        $html .= '</tbody></table>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Jera_Moyie_Register_" . date('Y-m-d') . ".pdf", ["Attachment" => true]);
        exit;
    } else {
        die("Error: Dompdf library not found.");
    }
}

// === NEW: HANDLE MEMBER DELETION ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_member'])) {
    $target_id = $_POST['target_id'];
    
    // Prevent the current logged-in user from deleting themselves
    if ($target_id == $_SESSION['member_id']) {
        $error = "You cannot delete your own account.";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
            $stmt->execute([$target_id]);
            log_admin_activity($pdo, "Member Deleted", "Deleted member ID: $target_id");
            $success = "Member removed from the register successfully.";
        } catch (PDOException $e) {
            $error = "Failed to remove member. They may have existing financial records.";
        }
    }
}

// === HANDLE NEW MEMBER ADDITION ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $name = trim($_POST['name']);
    $phone = preg_replace('/^0/', '254', trim($_POST['phone'])); 
    $nat_id = trim($_POST['national_id']);
    $is_native = $_POST['is_native'];
    $role = $_POST['role'];
    $is_admin = ($role === 'Member') ? 0 : 1;
    $pass = password_hash("Jera2026", PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO members (name, phone, national_id, is_native, role, is_admin, password_hash, joined_date, paid) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), 1)");
        $stmt->execute([$name, $phone, $nat_id, $is_native, $role, $is_admin, $pass]);
        log_admin_activity($pdo, "Member Added", "Name: $name, Phone: $phone, Role: $role");
        $success = "Member registered successfully.";
    } catch (PDOException $e) { $error = "Registration failed: " . $e->getMessage(); }
}

// === HANDLE UPDATES ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_member'])) {
    $target_id = $_POST['target_id'];
    $new_role = $_POST['role'];
    $new_status = $_POST['status'];
    $is_native = $_POST['is_native'];
    $is_admin = ($new_role === 'Member') ? 0 : 1;

    try {
        $stmt = $pdo->prepare("UPDATE members SET role = ?, status = ?, is_admin = ?, is_native = ? WHERE id = ?");
        $stmt->execute([$new_role, $new_status, $is_admin, $is_native, $target_id]);
        log_admin_activity($pdo, "Member Edited", "Member ID: $target_id - Role: $new_role, Status: $new_status");
        $success = "Member updated successfully.";
    } catch (PDOException $e) { $error = "Update failed: " . $e->getMessage(); }
}

$stmt = $pdo->query("SELECT * FROM members ORDER BY name ASC");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
$roles = ['Chairperson', 'Assistant Chairperson', 'Treasury', 'Secretary', 'Assistant Secretary', 'Organizer', 'Trustee', 'Loan Chairperson', 'Member', 'dev'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Register • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #1a5928; --bg: #f8f9fa; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; padding-top: 30px; }
        .register-card { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table thead { background: var(--primary); color: white; }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Member Register</h2>
            <p class="text-muted small">Manage Native & Community members</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">Add Member</button>
            <a href="?export_pdf=1" class="btn btn-danger rounded-pill px-4">Export PDF</a>
            <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-pill px-4"><i class="bi bi-grid me-1"></i>Console</a>
            <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-4">Profile</a>
        </div>
    </div>

    <?php if(!empty($success)): ?> <div class="alert alert-success border-0 shadow-sm"><?= $success ?></div> <?php endif; ?>
    <?php if(!empty($error)): ?> <div class="alert alert-danger border-0 shadow-sm"><?= $error ?></div> <?php endif; ?>

    <div class="register-card p-4 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>National ID</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $m): 
                        $displayPhone = preg_replace('/^254/', '0', $m['phone']);
                    ?>
                    <tr>
                        <td class="text-muted small">#<?= str_pad($m['id'], 3, '0', STR_PAD_LEFT) ?></td>
                        <td><a href="admin_member_profile.php?id=<?= $m['id'] ?>" class="text-decoration-none text-dark"><strong><?= htmlspecialchars($m['name']) ?></strong></a></td>
                        <td><?= htmlspecialchars($m['national_id']) ?></td>
                        <td class="small"><?= $displayPhone ?></td>
                        <td><span class="badge <?= $m['is_native'] ? 'bg-success' : 'bg-info' ?>"><?= $m['is_native'] ? 'Native' : 'Community' ?></span></td>
                        <td class="small"><?= $m['role'] ?></td>
                        <td><span class="badge bg-light text-dark border"><?= ucfirst($m['status']) ?></span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $m['id'] ?>">Edit</button>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this member?');" style="display:inline;">
                                    <input type="hidden" name="target_id" value="<?= $m['id'] ?>">
                                    <button type="submit" name="delete_member" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="editModal<?= $m['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 p-4 shadow">
                                <h5 class="fw-bold mb-3">Edit Member #<?= $m['id'] ?></h5>
                                <form method="POST">
                                    <input type="hidden" name="target_id" value="<?= $m['id'] ?>">
                                    <div class="mb-3">
                                        <label class="small fw-bold">Role</label>
                                        <select name="role" class="form-select">
                                            <?php foreach($roles as $r) echo "<option ".($m['role']==$r?'selected':'').">$r</option>"; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small fw-bold">Membership Type</label>
                                        <select name="is_native" class="form-select">
                                            <option value="1" <?=$m['is_native']?'selected':''?>>Native</option>
                                            <option value="0" <?=$m['is_native']?'':'selected'?>>Community</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small fw-bold">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" <?=$m['status']=='active'?'selected':''?>>Active</option>
                                            <option value="suspended" <?=$m['status']=='suspended'?'selected':''?>>Suspended</option>
                                            <option value="inactive" <?=$m['status']=='inactive'?'selected':''?>>Inactive</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_member" class="btn btn-success w-100 py-2 rounded-3">Update Member</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4 shadow">
            <h5 class="fw-bold mb-3">Add New Member</h5>
            <form method="POST">
                <div class="mb-3"><label class="small fw-bold">Full Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="small fw-bold">Phone</label><input type="text" name="phone" class="form-control" placeholder="07..." required></div>
                    <div class="col-6 mb-3"><label class="small fw-bold">National ID</label><input type="text" name="national_id" class="form-control" required></div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold">Type</label>
                        <select name="is_native" class="form-select">
                            <option value="1">Native</option>
                            <option value="0">Community</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold">Initial Role</label>
                        <select name="role" class="form-select">
                            <?php foreach($roles as $r) echo "<option>$r</option>"; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_member" class="btn btn-primary w-100 py-2 rounded-3">Register Member</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>