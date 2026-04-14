<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$event_id = $_GET['id'];

// Get event details
$sql = "SELECT * FROM events WHERE event_id = ? AND user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$event_id, $_SESSION['user_id']]);
$event = $stmt->fetch();

if(!$event) {
    header('Location: dashboard.php');
    exit();
}

// Get vendor total cost
$sql = "SELECT SUM(v.price) as total FROM event_vendors ev 
        JOIN vendors v ON ev.vendor_id = v.vendor_id 
        WHERE ev.event_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$event_id]);
$result = $stmt->fetch();
$vendor_total = $result['total'] ?? 0;

// Get venue cost
$venue_cost = 0;
if($event['venue_id']) {
    $sql = "SELECT price FROM venues WHERE venue_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$event['venue_id']]);
    $venue = $stmt->fetch();
    $venue_cost = $venue['price'] ?? 0;
}

$total_spent = $vendor_total + $venue_cost;
$remaining = $event['budget'] - $total_spent;
$percentage = ($total_spent / $event['budget']) * 100;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Budget - Event Planner</title>
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
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .budget-summary {
            text-align: center;
            margin-bottom: 30px;
        }
        .budget-label {
            font-size: 14px;
            color: #666;
        }
        .budget-number {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        .progress-bar {
            background: #e0e0e0;
            height: 30px;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-fill {
            background: #667eea;
            height: 100%;
            color: white;
            text-align: center;
            line-height: 30px;
            transition: width 0.3s;
        }
        .budget-item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .budget-item.total {
            background: #e0e7ff;
            font-weight: bold;
        }
        .remaining {
            text-align: center;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 20px;
            font-weight: bold;
        }
        .remaining.positive {
            background: #d4edda;
            color: #155724;
        }
        .remaining.negative {
            background: #f8d7da;
            color: #721c24;
        }
        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        .back:hover {
            text-decoration: underline;
        }
        .event-name {
            text-align: center;
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Budget Overview</h2>
    </div>

    <div class="container">
        <div class="event-name">
            📅 Event: <?php echo htmlspecialchars($event['event_name']); ?>
        </div>

        <div class="budget-summary">
            <div class="budget-label">Total Budget</div>
            <div class="budget-number"><?php echo number_format($event['budget'], 2); ?> BDT</div>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo min($percentage, 100); ?>%">
                <?php echo round($percentage); ?>%
            </div>
        </div>

        <div class="budget-item">
            <span>🏢 Venue Cost</span>
            <span><?php echo number_format($venue_cost, 2); ?> BDT</span>
        </div>

        <div class="budget-item">
            <span>📋 Vendors Total</span>
            <span><?php echo number_format($vendor_total, 2); ?> BDT</span>
        </div>

        <div class="budget-item total">
            <span>💰 Total Spent</span>
            <span><?php echo number_format($total_spent, 2); ?> BDT</span>
        </div>

        <div class="remaining <?php echo $remaining >= 0 ? 'positive' : 'negative'; ?>">
            <?php if($remaining >= 0): ?>
                ✅ Remaining Budget: <?php echo number_format($remaining, 2); ?> BDT
            <?php else: ?>
                ⚠️ Over Budget: <?php echo number_format(abs($remaining), 2); ?> BDT
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="back">← Back to Dashboard</a>
    </div>
</body>
</html>