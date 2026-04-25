<?php
require_once 'config.php';

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if user is admin (you can set admin status in database)
// For now, let's make user_id = 1 as admin
$is_admin = ($_SESSION['user_id'] == 1);

if(!$is_admin) {
    header('Location: dashboard.php');
    exit();
}

// Handle Add Venue
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_venue'])) {
    $sql = "INSERT INTO venues (venue_name, capacity, location, price) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['venue_name'], $_POST['capacity'], $_POST['location'], $_POST['price']]);
    header('Location: admin_dashboard.php?msg=venue_added');
    exit();
}

// Handle Add Vendor
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_vendor'])) {
    $sql = "INSERT INTO vendors (vendor_name, vendor_type, price) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['vendor_name'], $_POST['vendor_type'], $_POST['price']]);
    header('Location: admin_dashboard.php?msg=vendor_added');
    exit();
}

// Handle Delete Venue
if(isset($_GET['delete_venue'])) {
    $pdo->prepare("DELETE FROM venues WHERE venue_id = ?")->execute([$_GET['delete_venue']]);
    header('Location: admin_dashboard.php?msg=venue_deleted');
    exit();
}

// Handle Delete Vendor
if(isset($_GET['delete_vendor'])) {
    $pdo->prepare("DELETE FROM vendors WHERE vendor_id = ?")->execute([$_GET['delete_vendor']]);
    header('Location: admin_dashboard.php?msg=vendor_deleted');
    exit();
}

// Handle Update Event Status
if(isset($_GET['update_status'])) {
    $status = $_GET['status'];
    $event_id = $_GET['update_status'];
    $pdo->prepare("UPDATE events SET status = ? WHERE event_id = ?")->execute([$status, $event_id]);
    header('Location: admin_dashboard.php?msg=status_updated');
    exit();
}

// Handle Delete Event
if(isset($_GET['delete_event'])) {
    $pdo->prepare("DELETE FROM events WHERE event_id = ?")->execute([$_GET['delete_event']]);
    header('Location: admin_dashboard.php?msg=event_deleted');
    exit();
}

// Get all data for dashboard
$users = $pdo->query("SELECT * FROM users ORDER BY user_id DESC")->fetchAll();
$events = $pdo->query("SELECT e.*, u.username, v.venue_name 
                       FROM events e 
                       LEFT JOIN users u ON e.user_id = u.user_id
                       LEFT JOIN venues v ON e.venue_id = v.venue_id
                       ORDER BY e.event_id DESC")->fetchAll();
$venues = $pdo->query("SELECT * FROM venues ORDER BY venue_id DESC")->fetchAll();
$vendors = $pdo->query("SELECT * FROM vendors ORDER BY vendor_id DESC")->fetchAll();

// Get counts
$total_users = count($users);
$total_events = count($events);
$total_venues = count($venues);
$total_vendors = count($vendors);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Event Planner</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        
        /* Header */
        .header { background: #1a1a2e; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .logo-area { display: flex; align-items: center; gap: 15px; }
        .logo-icon { font-size: 28px; }
        .logo-text { font-size: 20px; font-weight: bold; }
        .logo-area a { text-decoration: none; color: white; display: flex; align-items: center; gap: 10px; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .logout-btn { background: #e74c3c; color: white; text-decoration: none; padding: 8px 20px; border-radius: 5px; }
        .logout-btn:hover { background: #c0392b; }
        
        /* Container */
        .container { max-width: 1400px; margin: 20px auto; padding: 0 20px; }
        
        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-number { font-size: 36px; font-weight: bold; color: #667eea; }
        .stat-label { color: #666; margin-top: 5px; }
        
        /* Sections */
        .section { background: white; border-radius: 10px; padding: 20px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .section-title { font-size: 20px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #667eea; display: flex; justify-content: space-between; align-items: center; }
        
        /* Forms */
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-danger { background: #e74c3c; }
        .btn-small { padding: 5px 10px; font-size: 12px; text-decoration: none; border-radius: 3px; }
        .btn-success { background: #27ae60; }
        .btn-warning { background: #f39c12; }
        
        /* Tables */
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        
        .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .status-planning { background: #f39c12; color: white; }
        .status-confirmed { background: #27ae60; color: white; }
        .status-completed { background: #3498db; color: white; }
        .status-cancelled { background: #e74c3c; color: white; }
        
        .action-links a { margin-right: 10px; text-decoration: none; font-size: 12px; }
        
        .message { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .form-grid { grid-template-columns: 1fr; }
            table { font-size: 12px; }
            th, td { padding: 8px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-area">
            <a href="admin_dashboard.php">
                <div class="logo-icon">👑</div>
                <div class="logo-text">Admin Panel</div>
            </a>
        </div>
        <div class="user-info">
            Admin: <?php echo $_SESSION['username']; ?>
            <a href="dashboard.php" class="logout-btn" style="background: #667eea;">User View</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if(isset($_GET['msg'])): ?>
            <div class="message">✅ Action completed successfully!</div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_events; ?></div>
                <div class="stat-label">Total Events</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_venues; ?></div>
                <div class="stat-label">Total Venues</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_vendors; ?></div>
                <div class="stat-label">Total Vendors</div>
            </div>
        </div>

        <!-- Add New Venue -->
        <div class="section">
            <div class="section-title">
                <span>➕ Add New Venue</span>
            </div>
            <form method="POST" class="form-grid">
                <div class="form-group">
                    <input type="text" name="venue_name" placeholder="Venue Name" required>
                </div>
                <div class="form-group">
                    <input type="number" name="capacity" placeholder="Capacity" required>
                </div>
                <div class="form-group">
                    <input type="text" name="location" placeholder="Location" required>
                </div>
                <div class="form-group">
                    <input type="number" name="price" placeholder="Price (BDT)" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="add_venue" class="btn">Add Venue</button>
                </div>
            </form>
        </div>

        <!-- Add New Vendor -->
        <div class="section">
            <div class="section-title">
                <span>➕ Add New Vendor</span>
            </div>
            <form method="POST" class="form-grid">
                <div class="form-group">
                    <input type="text" name="vendor_name" placeholder="Vendor Name" required>
                </div>
                <div class="form-group">
                    <select name="vendor_type" required>
                        <option value="">Select Type</option>
                        <option value="Catering">Catering</option>
                        <option value="Photography">Photography</option>
                        <option value="Decoration">Decoration</option>
                        <option value="Music">Music</option>
                        <option value="Flowers">Flowers</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="number" name="price" placeholder="Price (BDT)" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="add_vendor" class="btn">Add Vendor</button>
                </div>
            </form>
        </div>

        <!-- Manage Venues -->
        <div class="section">
            <div class="section-title">
                <span>🏢 Manage Venues</span>
            </div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Capacity</th><th>Location</th><th>Price</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach($venues as $venue): ?>
                    <tr>
                        <td><?php echo $venue['venue_id']; ?></td>
                        <td><?php echo htmlspecialchars($venue['venue_name']); ?></td>
                        <td><?php echo $venue['capacity']; ?></td>
                        <td><?php echo $venue['location']; ?></td>
                        <td><?php echo number_format($venue['price'], 2); ?> BDT</td>
                        <td class="action-links">
                            <a href="?delete_venue=<?php echo $venue['venue_id']; ?>" class="btn-small btn-danger" onclick="return confirm('Delete this venue?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Manage Vendors -->
        <div class="section">
            <div class="section-title">
                <span>📋 Manage Vendors</span>
            </div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Type</th><th>Price</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach($vendors as $vendor): ?>
                    <tr>
                        <td><?php echo $vendor['vendor_id']; ?></td>
                        <td><?php echo htmlspecialchars($vendor['vendor_name']); ?></td>
                        <td><?php echo $vendor['vendor_type']; ?></td>
                        <td><?php echo number_format($vendor['price'], 2); ?> BDT</td>
                        <td class="action-links">
                            <a href="?delete_vendor=<?php echo $vendor['vendor_id']; ?>" class="btn-small btn-danger" onclick="return confirm('Delete this vendor?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Manage Users & Events -->
        <div class="section">
            <div class="section-title">
                <span>👥 Users & Their Events</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Event ID</th><th>Event Name</th><th>User</th><th>Date</th><th>Guests</th><th>Budget</th><th>Venue</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($events as $event): ?>
                    <tr>
                        <td><?php echo $event['event_id']; ?></td>
                        <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                        <td><?php echo $event['username']; ?></td>
                        <td><?php echo $event['event_date']; ?></td>
                        <td><?php echo $event['no_of_guests']; ?></td>
                        <td><?php echo number_format($event['budget'], 2); ?> BDT</td>
                        <td><?php echo $event['venue_name'] ?? 'Not selected'; ?></td>
                        <td>
                            <span class="status status-<?php echo $event['status'] ?? 'planning'; ?>">
                                <?php echo $event['status'] ?? 'planning'; ?>
                            </span>
                        </td>
                        <td class="action-links">
                            <a href="?update_status=<?php echo $event['event_id']; ?>&status=planning" class="btn-small btn-warning">Planning</a>
                            <a href="?update_status=<?php echo $event['event_id']; ?>&status=confirmed" class="btn-small btn-success">Confirm</a>
                            <a href="?update_status=<?php echo $event['event_id']; ?>&status=completed" class="btn-small" style="background:#3498db;color:white;">Complete</a>
                            <a href="?delete_event=<?php echo $event['event_id']; ?>" class="btn-small btn-danger" onclick="return confirm('Delete this event?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>