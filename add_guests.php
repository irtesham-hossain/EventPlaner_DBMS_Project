<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$event_id = $_GET['id'];

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_guest'])) {
    $sql = "INSERT INTO guests (event_id, guest_name, guest_phone) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$event_id, $_POST['guest_name'], $_POST['guest_phone']]);
    
    // Update guest count
    $pdo->prepare("UPDATE events SET no_of_guests = no_of_guests + 1 WHERE event_id = ?")->execute([$event_id]);
    
    header("Location: add_guests.php?id=$event_id");
    exit();
}

// Delete guest
if(isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM guests WHERE guest_id = ?")->execute([$_GET['delete']]);
    $pdo->prepare("UPDATE events SET no_of_guests = no_of_guests - 1 WHERE event_id = ?")->execute([$event_id]);
    header("Location: add_guests.php?id=$event_id");
    exit();
}

$guests = $pdo->prepare("SELECT * FROM guests WHERE event_id = ?");
$guests->execute([$event_id]);
$guests = $guests->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Guests - Event Planner</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #667eea; color: white; padding: 15px 30px; }
        .container { max-width: 800px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .guest-item { background: #f9f9f9; padding: 15px; margin: 10px 0; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; }
        .guest-count { background: #e0e7ff; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .back { display: block; text-align: center; margin-top: 20px; color: #667eea; text-decoration: none; }
        .delete-btn { background: #e74c3c; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; }
        .guest-name { font-size: 16px; font-weight: bold; }
        .guest-phone { color: #666; font-size: 14px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Manage Guests</h2>
    </div>
    <div class="container">
        <div class="guest-count">📊 Total Guests: <?php echo count($guests); ?></div>
        
        <h3>Add New Guest</h3>
        <form method="POST">
            <input type="text" name="guest_name" placeholder="Guest Name" required>
            <input type="text" name="guest_phone" placeholder="Phone Number">
            <button type="submit" name="add_guest">Add Guest</button>
        </form>

        <h3>Guest List</h3>
        <?php foreach($guests as $guest): ?>
            <div class="guest-item">
                <div>
                    <div class="guest-name">👤 <?php echo htmlspecialchars($guest['guest_name']); ?></div>
                    <?php if($guest['guest_phone']): ?>
                        <div class="guest-phone">📞 <?php echo $guest['guest_phone']; ?></div>
                    <?php endif; ?>
                </div>
                <a href="?id=<?php echo $event_id; ?>&delete=<?php echo $guest['guest_id']; ?>" class="delete-btn" onclick="return confirm('Remove this guest?')">Remove</a>
            </div>
        <?php endforeach; ?>
        
        <a href="dashboard.php" class="back">← Back to Dashboard</a>
    </div>
</body>
</html>