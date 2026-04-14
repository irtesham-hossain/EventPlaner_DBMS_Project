<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "INSERT INTO events (user_id, event_name, event_date, no_of_guests, budget) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$_SESSION['user_id'], $_POST['event_name'], $_POST['event_date'], $_POST['no_of_guests'], $_POST['budget']])) {
        header('Location: dashboard.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #667eea; color: white; padding: 15px 30px; }
        .container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .back { display: block; text-align: center; margin-top: 20px; color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Create New Event</h2>
    </div>
    <div class="container">
        <form method="POST">
            <input type="text" name="event_name" placeholder="Event Name (e.g., Wedding, Birthday)" required>
            <input type="date" name="event_date" required>
            <input type="number" name="no_of_guests" placeholder="Number of Guests" required>
            <input type="number" name="budget" placeholder="Total Budget (BDT)" required>
            <button type="submit">Create Event</button>
        </form>
        <a href="dashboard.php" class="back">← Back</a>
    </div>
</body>
</html>