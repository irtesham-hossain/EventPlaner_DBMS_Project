<?php
require_once 'config.php';

$error = '';
$success = '';

// Check if already logged in
if(isset($_SESSION['user_id'])) {
    // Redirect based on role
    if($_SESSION['role'] == 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)) {
        $error = "Please enter both username/email and password";
    } else {
        // Check if user exists (by username or email)
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if($user) {
            // Verify password
            if(password_verify($password, $user['password'])) {
                // Login successful - store session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'] ?? 'user';
                
                // Redirect based on role
                if($_SESSION['role'] == 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "User not found! Please check your username/email or register.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Event Planner</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center;
        }
        .container { 
            background: white; 
            padding: 40px; 
            border-radius: 10px; 
            width: 400px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .logo { 
            text-align: center; 
            margin-bottom: 30px;
        }
        .logo-icon { 
            font-size: 48px; 
        }
        .logo-text { 
            font-size: 24px; 
            font-weight: bold; 
            color: #667eea; 
            margin-top: 5px;
        }
        h2 { 
            text-align: center; 
            margin-bottom: 30px; 
            color: #333;
        }
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            font-size: 14px;
        }
        input:focus { 
            outline: none; 
            border-color: #667eea; 
        }
        button { 
            width: 100%; 
            padding: 12px; 
            background: #667eea; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover { 
            background: #5a67d8; 
        }
        .error { 
            background: #fee; 
            color: #e74c3c; 
            text-align: center; 
            margin-bottom: 15px; 
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #e74c3c;
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            text-align: center; 
            margin-bottom: 15px; 
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #27ae60;
        }
        .link { 
            text-align: center; 
            margin-top: 20px; 
        }
        .link a { 
            color: #667eea; 
            text-decoration: none; 
        }
        .link a:hover { 
            text-decoration: underline; 
        }
        .info-text {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <div class="logo-icon">📅</div>
            <div class="logo-text">Event Planner</div>
        </div>
        
        <h2>Login to Your Account</h2>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
            <div class="success">✅ Registration successful! Please login.</div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username or Email" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
        <div class="link">
            <a href="register.php">Don't have an account? Register here</a>
        </div>
        
        <div class="info-text">
            Demo Credentials:<br>
            Admin: username "admin" | password "admin123"<br>
            User: Register a new account
        </div>
    </div>
</body>
</html>