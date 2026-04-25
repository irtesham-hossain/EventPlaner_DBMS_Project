<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$event_id = $_GET['id'];

// Get all vendors
$sql = "SELECT * FROM vendors";
$vendors = $pdo->query($sql)->fetchAll();

// Get selected vendors for this event
$sql = "SELECT vendor_id FROM event_vendors WHERE event_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$event_id]);
$selected = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Select vendor
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vendor_id'])) {
    $vendor_id = $_POST['vendor_id'];
    
    // Check if already selected
    $sql = "SELECT * FROM event_vendors WHERE event_id = ? AND vendor_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$event_id, $vendor_id]);
    
    if(!$stmt->fetch()) {
        $sql = "INSERT INTO event_vendors (event_id, vendor_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$event_id, $vendor_id]);
    }
    
    header("Location: select_vendor.php?id=$event_id");
    exit();
}

// Remove vendor
if(isset($_GET['remove'])) {
    $vendor_id = $_GET['remove'];
    $sql = "DELETE FROM event_vendors WHERE event_id = ? AND vendor_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$event_id, $vendor_id]);
    header("Location: select_vendor.php?id=$event_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Vendors - Event Planner</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .header {
            background: #667eea;
            color: white;
            padding: 15px 30px;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .vendor-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .vendor-name {
            font-size: 18px;
            font-weight: bold;
        }
        .vendor-details {
            color: #666;
            margin: 5px 0;
        }
        .select-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .selected {
            background: #e0e7ff;
            border-color: #667eea;
        }
        .remove-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        .section-title {
            margin: 20px 0 10px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Select Vendors</h2>
    </div>

    <div class="container">
        <h3 class="section-title">Selected Vendors</h3>
        <?php if(count($selected) > 0): ?>
            <?php foreach($selected as $vid): 
                $sql = "SELECT * FROM vendors WHERE vendor_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$vid]);
                $vendor = $stmt->fetch();
            ?>
                <div class="vendor-card selected">
                    <div class="vendor-name">✓ <?php echo htmlspecialchars($vendor['vendor_name']); ?></div>
                    <div class="vendor-details">📋 Type: <?php echo $vendor['vendor_type']; ?></div>
                    <div class="vendor-details">💰 Price: $<?php echo number_format($vendor['price'], 2); ?></div>
                    <a href="?id=<?php echo $event_id; ?>&remove=<?php echo $vid; ?>" class="remove-btn">Remove</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No vendors selected yet.</p>
        <?php endif; ?>

        <h3 class="section-title">Available Vendors</h3>
        <form method="POST">
            <?php foreach($vendors as $vendor): ?>
                <?php if(!in_array($vendor['vendor_id'], $selected)): ?>
                    <div class="vendor-card">
                        <div class="vendor-name"><?php echo htmlspecialchars($vendor['vendor_name']); ?></div>
                        <div class="vendor-details">📋 Type: <?php echo $vendor['vendor_type']; ?></div>
                        <div class="vendor-details">💰 Price: $<?php echo number_format($vendor['price'], 2); ?></div>
                        <button type="submit" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>" class="select-btn">Select</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </form>
        <a href="dashboard.php" class="back">← Back to Dashboard</a>
    </div>
</body>
</html>