<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user's events
$sql = "SELECT * FROM events WHERE user_id = ? ORDER BY event_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .welcome { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .btn { background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .event-card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .event-title { font-size: 20px; font-weight: bold; margin-bottom: 10px; }
        .event-details { color: #666; margin: 10px 0; }
        .event-actions { margin-top: 15px; }
        .event-actions a { margin-right: 10px; color: #667eea; text-decoration: none; }
        .logout { color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Event Planner</h2>
        <div>Welcome, <?php echo $_SESSION['username']; ?> | <a href="logout.php" class="logout">Logout</a></div>
    </div>

    <div class="container">
        <div class="welcome">
            <h3>Plan Your Event</h3>
            <p>Follow these steps to create your perfect event:</p>
            <br>
            <a href="add_event.php" class="btn">+ Create New Event</a>
        </div>

        <h3>My Events</h3>
        <?php if(count($events) > 0): ?>
            <?php foreach($events as $event): ?>
                <div class="event-card">
                    <div class="event-title">🎉 <?php echo htmlspecialchars($event['event_name']); ?></div>
                    <div class="event-details">
                        📅 Date: <?php echo $event['event_date']; ?><br>
                        👥 Guests: <?php echo $event['no_of_guests']; ?><br>
                        💰 Budget: <?php echo number_format($event['budget'], 2); ?> BDT
                    </div>
                    <div class="event-actions">
                        <a href="select_venue.php?id=<?php echo $event['event_id']; ?>">🏢 Select Venue</a> |
                        <a href="add_guests.php?id=<?php echo $event['event_id']; ?>">👥 Add Guests</a> |
                        <a href="select_vendor.php?id=<?php echo $event['event_id']; ?>">📋 Select Vendors</a> |
                        <a href="budget.php?id=<?php echo $event['event_id']; ?>">💰 Budget</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No events yet. Click "Create New Event" to get started!</p>
        <?php endif; ?>
    </div>
</body>
</html>