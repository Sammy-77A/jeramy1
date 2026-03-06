<?php
require 'config.php';

// Security: Organizer, Secretary, or Chairperson only
$allowed_roles = ['Organizer', 'Secretary', 'Assistant Secretary', 'Chairperson', 'dev'];
if (!isset($_SESSION['member_id']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header('Location: profile.php');
    exit;
}

$success = '';
$error = '';

// --- PDF DOWNLOAD LOGIC ---
if (isset($_GET['download_minutes'])) {
    $date = $_GET['download_minutes'];
    $pdfPath = __DIR__ . '/dompdf/autoload.inc.php'; 
    if (file_exists($pdfPath)) {
        require_once $pdfPath;
        $dompdf = new \Dompdf\Dompdf();

        $stmt = $pdo->prepare("SELECT m.*, mem.name as recorder FROM meeting_minutes m JOIN members mem ON m.recorder_id = mem.id WHERE m.meeting_date = ?");
        $stmt->execute([$date]);
        $meeting = $stmt->fetch();

        $stmt_att = $pdo->prepare("SELECT a.*, mem.name FROM attendance a JOIN members mem ON a.member_id = mem.id WHERE a.meeting_date = ? ORDER BY mem.name ASC");
        $stmt_att->execute([$date]);
        $attendance = $stmt_att->fetchAll();

        $html = "
        <style>
            body { font-family: sans-serif; color: #333; font-size: 12px; }
            .header { text-align: center; border-bottom: 2px solid #1a5928; padding-bottom: 10px; }
            h2 { color: #1a5928; margin-bottom: 5px; }
            .minutes { white-space: pre-wrap; margin: 20px 0; line-height: 1.5; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background: #1a5928; color: white; padding: 8px; border: 1px solid #ddd; text-align: left; }
            td { padding: 8px; border: 1px solid #ddd; vertical-align: top; }
            .apology { font-style: italic; color: #666; font-size: 10px; }
        </style>
        <div class='header'>
            <h2>Jera Moyie Meeting Minutes</h2>
            <p>Date: " . date('d F Y', strtotime($date)) . "</p>
        </div>
        <p><strong>Recorder:</strong> {$meeting['recorder']}</p>
        <div class='minutes'><strong>Minutes:</strong><br>{$meeting['minutes_text']}</div>
        <h3>Attendance & Apologies</h3>
        <table>
            <thead><tr><th>Member Name</th><th>Status</th><th>Apology/Notes</th></tr></thead>
            <tbody>";
        foreach ($attendance as $att) {
            $html .= "<tr>
                <td>{$att['name']}</td>
                <td>" . ucfirst($att['status']) . "</td>
                <td class='apology'>" . htmlspecialchars($att['apology_text'] ?? '-') . "</td>
            </tr>";
        }
        $html .= "</tbody></table>";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Minutes_$date.pdf");
        exit;
    }
}

// --- HANDLE SUBMISSION (FIXED SAVE LOGIC) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_session'])) {
    $meeting_date = $_POST['meeting_date'];
    $minutes = $_POST['minutes'];
    $status_data = $_POST['status']; 
    $apologies = $_POST['apology_text']; // member_id => text

    try {
        $pdo->beginTransaction();

        // 1. MUST save meeting_minutes first for Foreign Key integrity
        $stmt_min = $pdo->prepare("INSERT INTO meeting_minutes (meeting_date, recorder_id, minutes_text) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE minutes_text = ?");
        $stmt_min->execute([$meeting_date, $_SESSION['member_id'], $minutes, $minutes]);

        // 2. Save Attendance & Apologies
        $stmt_att = $pdo->prepare("INSERT INTO attendance (member_id, meeting_date, status, apology_text) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = ?, apology_text = ?");
        foreach ($status_data as $m_id => $status) {
            $apology = isset($apologies[$m_id]) ? $apologies[$m_id] : null;
            $stmt_att->execute([$m_id, $meeting_date, $status, $apology, $status, $apology]);
        }

        $pdo->commit();
        log_admin_activity($pdo, "Meeting Recorded", "Date: $meeting_date - Attendance and minutes saved");
        $success = "Meeting records and attendance for $meeting_date saved successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error saving attendance: " . $e->getMessage();
    }
}

$members = $pdo->query("SELECT id, name FROM members WHERE status = 'active' ORDER BY name ASC")->fetchAll();
$past_meetings = $pdo->query("SELECT * FROM meeting_minutes ORDER BY meeting_date DESC LIMIT 10")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meeting Logs • Jera Moyie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #1a5928; --bg: #f4f7f6; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; padding-top: 20px; }
        .session-card { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .minutes-area { border: 1px solid #dee2e6; border-radius: 10px; padding: 15px; min-height: 250px; }
        .apology-input { font-size: 0.75rem; border: none; border-bottom: 1px dashed #ccc; background: transparent; width: 100%; }
        .apology-input:focus { outline: none; border-bottom-color: var(--primary); }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Meeting Logs</h2>
            <p class="text-muted small">Attendance & Minutes</p>
        </div>
        <div class="d-flex gap-2">
            <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-pill px-4 shadow-sm"><i class="bi bi-chevron-left me-1"></i>Console</a>
            <a href="profile.php" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">Profile</a>
        </div>
    </div>

    <?php if($success): ?> <div class="alert alert-success border-0 shadow-sm mb-3"><?= $success ?></div> <?php endif; ?>
    <?php if($error): ?> <div class="alert alert-danger border-0 shadow-sm mb-3"><?= $error ?></div> <?php endif; ?>

    <form method="POST">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card session-card p-4">
                    <div class="mb-4">
                        <label class="small fw-bold mb-1">Meeting Date</label>
                        <input type="date" name="meeting_date" class="form-control w-auto" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3"><i class="bi bi-person-check me-2"></i>Attendance & Apologies</h6>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead><tr class="small text-muted"><th>Member</th><th>Status</th><th>Apology</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($members as $m): ?>
                                        <tr>
                                            <td class="small fw-bold"><?= htmlspecialchars($m['name']) ?></td>
                                            <td>
                                                <select name="status[<?= $m['id'] ?>]" class="form-select form-select-sm border-0 bg-light" onchange="toggleApology(this, <?= $m['id'] ?>)">
                                                    <option value="present">Present</option>
                                                    <option value="late">Late</option>
                                                    <option value="absent">Absent</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="apology_text[<?= $m['id'] ?>]" id="apology_<?= $m['id'] ?>" class="apology-input" placeholder="Reason...">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3"><i class="bi bi-journal-text me-2"></i>Minutes</h6>
                            <textarea name="minutes" class="form-control minutes-area" placeholder="Enter key deliberations and decisions..." required></textarea>
                            <button type="submit" name="save_session" class="btn btn-primary w-100 py-2 mt-3 rounded-pill shadow">Save Meeting Records</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card session-card p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Recent Sessions</h6>
                    <?php foreach ($past_meetings as $pm): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <div class="fw-bold small"><?= date('D, d M Y', strtotime($pm['meeting_date'])) ?></div>
                            </div>
                            <a href="?download_minutes=<?= $pm['meeting_date'] ?>" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-file-earmark-pdf fs-5"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleApology(select, id) {
        const input = document.getElementById('apology_' + id);
        if (select.value === 'absent') {
            input.placeholder = "Enter apology reason...";
            input.style.borderBottomColor = "#dc3545";
        } else {
            input.placeholder = "Notes...";
            input.style.borderBottomColor = "#ccc";
        }
    }
</script>
</body>
</html>