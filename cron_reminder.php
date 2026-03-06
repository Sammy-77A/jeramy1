<?php
// cron_reminder.php
require 'config.php';
require_once __DIR__ . '/includes/NotificationService.php';

// Fetch all members with an email address
$stmt = $pdo->query("SELECT id, name, email FROM members WHERE email IS NOT NULL AND email != ''");
$members = $stmt->fetchAll();

foreach ($members as $member) {
    $message = "This is a friendly reminder for our <strong>Weekly Group Meeting</strong> held at Kibuye Market, Gate 3, between 4:00 PM and 6:00 PM.<br><br>
                Your active participation is vital as we continue to grow through our core pillars:<br>
                <ul>
                    <li><strong>Smart Savings:</strong> Secure plans with no hidden fees and annual dividends.</li>
                    <li><strong>Table Banking:</strong> Access to instant credit up to 3x your savings.</li>
                    <li><strong>Social Welfare:</strong> Mutual support during emergencies.</li>
                </ul>
                We value your consistency in making Jera Moyie Kisumu's most trusted self-help group. See you there!";

    NotificationService::notify($member['id'], "Weekly Group Meeting Reminder", $message, "reminder");
}
