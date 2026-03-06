<?php
// events.php - Upcoming Events Page
require 'config.php'; // If needed for dynamic content

// Sample static events (you can make this dynamic from DB later)
$events = [
    [
        'title' => 'Weekly Table Banking Meeting',
        'date' => 'Every Monday',
        'time' => '4:00 PM - 6:00 PM',
        'location' => 'Kibuye Market, Gate 3',
        'description' => 'Join us for our regular meeting to discuss savings, loans, and group updates.'
    ],
    [
        'title' => 'Annual Profit Sharing Ceremony',
        'date' => 'December 20, 2025',
        'time' => '10:00 AM - 2:00 PM',
        'location' => 'Kisumu Social Hall',
        'description' => 'Celebrate the year\'s achievements and share group profits among members.'
    ],
    [
        'title' => 'Financial Literacy Workshop',
        'date' => 'January 15, 2026',
        'time' => '9:00 AM - 12:00 PM',
        'location' => 'Online via Zoom',
        'description' => 'Learn about smart saving, investing, and managing loans from expert speakers.'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events | Jera Moyie Self-Help Group</title>
    <meta name="description" content="Check out upcoming events, meetings, and workshops at Jera Moyie Self-Help Group in Kisumu.">
    <link rel="icon" type="image/icon" href="images/jm.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />

    <style>
        :root {
            --green: #2c7a4b;
            --orange: #f4a261;
            --light: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: #333;
        }

        h1,
        h2 {
            font-family: 'Playfair Display', serif;
        }

        .event-card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .event-card h3 {
            color: var(--green);
        }

        .event-card .date {
            font-weight: bold;
            color: var(--orange);
        }
    </style>
</head>

<body>

    <!-- Hero Section -->
    <section class="hero bg-success text-white text-center py-5">
        <div class="container">
            <h1>Upcoming Events</h1>
            <p>Stay updated with Jera Moyie's meetings, workshops, and special events.</p>
        </div>
    </section>

    <div class="container py-5">
        <?php foreach ($events as $event): ?>
            <div class="event-card bg-white" data-aos="fade-up">
                <h3><?= htmlspecialchars($event['title']) ?></h3>
                <p class="date"><i class="bi bi-calendar-event"></i> <?= htmlspecialchars($event['date']) ?> | <i class="bi bi-clock"></i> <?= htmlspecialchars($event['time']) ?></p>
                <p><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($event['location']) ?></p>
                <p><?= htmlspecialchars($event['description']) ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (empty($events)): ?>
            <p class="text-center text-muted">No upcoming events at the moment. Check back soon!</p>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>© 2025 Jera Moyie Self-Help Group. All rights reserved.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000
        });
    </script>
</body>

</html>