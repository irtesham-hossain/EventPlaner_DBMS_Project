<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$event_id = $_GET['id'];
$vendors = $pdo->query("SELECT * FROM vendors")->fetchAll();


$selected = $pdo->prepare("SELECT vendor_id FROM event_vendors WHERE event_id = ?");
$selected->execute([$event_id]);
$selected = $selected->fetchAll(PDO::FETCH_COLUMN);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vendor_id'])) {
    $check = $pdo->prepare("SELECT * FROM event_vendors WHERE event_id = ? AND vendor_id = ?");
    $check->execute([$event_id, $_POST['vendor_id']]);
    if(!$check->fetch()) {
        $insert = $pdo->prepare("INSERT INTO event_vendors (event_id, vendor_id) VALUES (?, ?)");
        $insert->execute([$event_id, $_POST['vendor_id']]);
    }
    header("Location: select_vendor.php?id=$event_id");
    exit();
}

if(isset($_GET['remove'])) {
    $pdo->prepare("DELETE FROM event_vendors WHERE event_id = ? AND vendor_id = ?")->execute([$event_id, $_GET['remove']]);
    header("Location: select_vendor.php?id=$event_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Vendors</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #667eea; color: white; padding: 15px 30px; }
        .container { max-width: 800px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; }
        .vendor-card { border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .selected { background: #e0e7ff; border-color: #667eea; }
        button { background: #667eea; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        .remove-btn { background: #e74c3c; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; display: inline-block; margin-left: 10px; }
        .back { display: block; text-align: center; margin-top: 20px; color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Select Vendors</h2>
    </div>
    <div class="container">
        <h3>Selected Vendors</h3>
        <?php foreach($selected as $vid): 
            $vendor = $pdo->prepare("SELECT * FROM vendors WHERE vendor_id = ?");
            $vendor->execute([$vid]);
            $v = $vendor->fetch();
        ?>
            <div class="vendor-card selected">
                ✓ <?php echo $v['vendor_name']; ?> - <?php echo $v['vendor_type']; ?> - <?php echo number_format($v['price'], 2); ?> BDT
                <a href="?id=<?php echo $event_id; ?>&remove=<?php echo $vid; ?>" class="remove-btn">Remove</a>
            </div>
        <?php endforeach; ?>

        <h3>Available Vendors</h3>
        <form method="POST">
            <?php foreach($vendors as $vendor): ?>
                <?php if(!in_array($vendor['vendor_id'], $selected)): ?>
                    <div class="vendor-card">
                        <b><?php echo $vendor['vendor_name']; ?></b> - <?php echo $vendor['vendor_type']; ?> - <?php echo number_format($vendor['price'], 2); ?> BDT
                        <button type="submit" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">Select</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </form>
        <a href="dashboard.php" class="back">← Back</a>
    </div>
</body>
</html>