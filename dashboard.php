<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user's events with venue details
$sql = "SELECT e.*, v.venue_name, v.location 
        FROM events e
        LEFT JOIN venues v ON e.venue_id = v.venue_id
        WHERE e.user_id = ? 
        ORDER BY e.event_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Event Planner</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .logo-area { display: flex; align-items: center; gap: 15px; cursor: pointer; transition: transform 0.3s; }
        .logo-area:hover { transform: scale(1.05); }
        .logo-icon { font-size: 28px; }
        .logo-text { font-size: 20px; font-weight: bold; }
        .logo-area a { text-decoration: none; color: white; display: flex; align-items: center; gap: 10px; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .logout-btn { 
            background: #e74c3c; 
            color: white; 
            text-decoration: none; 
            padding: 8px 20px; 
            border-radius: 5px; 
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .logout-btn:hover { background: #c0392b; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .welcome { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .event-card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .event-title { font-size: 20px; font-weight: bold; margin-bottom: 10px; color: #333; }
        .event-details { color: #666; margin: 10px 0; line-height: 1.8; }
        .event-details div { margin: 5px 0; }
        .selected-info { background: #e8f0fe; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .selected-label { font-weight: bold; color: #667eea; }
        .vendor-list { list-style: none; padding-left: 0; }
        .vendor-list li { display: inline-block; background: #e0e7ff; padding: 3px 10px; border-radius: 15px; margin: 3px; font-size: 12px; }
        .event-actions { margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee; }
        .event-actions a { margin-right: 15px; color: #667eea; text-decoration: none; font-size: 14px; }
        .event-actions a:hover { text-decoration: underline; }
        h3 { margin-bottom: 15px; color: #333; }
        .empty-state { text-align: center; padding: 40px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-area">
            <a href="login.php">
                <div class="logo-icon">📅</div>
                <div class="logo-text">Event Planner</div>
            </a>
        </div>
        
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
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
            <?php foreach($events as $event): 
                $vendor_sql = "SELECT v.vendor_name, v.vendor_type, v.price 
                               FROM event_vendors ev 
                               JOIN vendors v ON ev.vendor_id = v.vendor_id 
                               WHERE ev.event_id = ?";
                $vendor_stmt = $pdo->prepare($vendor_sql);
                $vendor_stmt->execute([$event['event_id']]);
                $vendors = $vendor_stmt->fetchAll();
            ?>
                <div class="event-card">
                    <div class="event-title">🎉 <?php echo htmlspecialchars($event['event_name']); ?></div>
                    <div class="event-details">
                        <div>📅 Date: <?php echo $event['event_date']; ?></div>
                        <div>👥 Guests: <?php echo $event['no_of_guests']; ?></div>
                        <div>💰 Budget: <?php echo number_format($event['budget'], 2); ?> BDT</div>
                        
                        <div class="selected-info">
                            <span class="selected-label">🏢 Selected Venue:</span><br>
                            <?php if($event['venue_name']): ?>
                                ✅ <?php echo htmlspecialchars($event['venue_name']); ?> 
                                (📍 <?php echo $event['location']; ?>)
                            <?php else: ?>
                                ⚠️ No venue selected yet
                            <?php endif; ?>
                        </div>
                        
                        <div class="selected-info">
                            <span class="selected-label">📋 Selected Vendors:</span><br>
                            <?php if(count($vendors) > 0): ?>
                                <ul class="vendor-list">
                                    <?php foreach($vendors as $vendor): ?>
                                        <li>✅ <?php echo htmlspecialchars($vendor['vendor_name']); ?> 
                                            (<?php echo $vendor['vendor_type']; ?> - <?php echo number_format($vendor['price'], 2); ?> BDT)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                ⚠️ No vendors selected yet
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="event-actions">
                        <a href="select_venue.php?id=<?php echo $event['event_id']; ?>">🏢 Change Venue</a>
                        <a href="select_vendor.php?id=<?php echo $event['event_id']; ?>">📋 Manage Vendors</a>
                        <a href="add_guests.php?id=<?php echo $event['event_id']; ?>">👥 Manage Guests</a>
                        <a href="budget.php?id=<?php echo $event['event_id']; ?>">💰 View Budget</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>No events yet. Click "Create New Event" to get started!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>