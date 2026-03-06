<?php
// trigger_reminder.php
require 'config.php';
require 'EmailService.php';

$emailService = new EmailService();

// Fetch all members with an email address
$stmt = $pdo->query("SELECT name, email FROM members WHERE email IS NOT NULL AND email != ''");
$members = $stmt->fetchAll();

$count = 0;
foreach ($members as $member) {
    $result = $emailService->sendMeetingReminder($member['email'], $member['name']);
    if ($result['success']) {
        $count++;
    }
}

echo "Reminder sent successfully to $count members.";
