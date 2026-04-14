<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$event_id = $_GET['id'];
$venues = $pdo->query("SELECT * FROM venues")->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "UPDATE events SET venue_id = ? WHERE event_id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['venue_id'], $event_id, $_SESSION['user_id']]);
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Venue</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #667eea; color: white; padding: 15px 30px; }
        .container { max-width: 800px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; }
        .venue-card { border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .venue-name { font-size: 18px; font-weight: bold; }
        button { background: #667eea; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .back { display: block; text-align: center; margin-top: 20px; color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Select Venue</h2>
    </div>
    <div class="container">
        <form method="POST">
            <?php foreach($venues as $venue): ?>
                <div class="venue-card">
                    <div class="venue-name">🏢 <?php echo $venue['venue_name']; ?></div>
                    <div>📍 Location: <?php echo $venue['location']; ?></div>
                    <div>👥 Capacity: <?php echo $venue['capacity']; ?> guests</div>
                    <div>💰 Price: <?php echo number_format($venue['price'], 2); ?> BDT</div>
                    <button type="submit" name="venue_id" value="<?php echo $venue['venue_id']; ?>">Select This Venue</button>
                </div>
            <?php endforeach; ?>
        </form>
        <a href="dashboard.php" class="back">← Back</a>
    </div>
</body>
</html>