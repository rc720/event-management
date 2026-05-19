<?php
// Initialize database connection
require_once __DIR__ . '/../config/db.php';

// If already logged in, bypass to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$errorMsg = '';

// Handle credentials submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $errorMsg = 'Please fill in both fields.';
    } else {
        try {
            // Prepared statement query
            $stmt = $pdo->prepare("SELECT * FROM `admin_users` WHERE `username` = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Perfect match! Establish secure session states
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_fullname'] = $admin['fullname'];
                
                header('Location: index.php');
                exit;
            } else {
                $errorMsg = 'Invalid Username or Password!';
            }
        } catch (Exception $e) {
            $errorMsg = 'System error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuraEvents | Secure Admin Login</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Admin CSS -->
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="bg-primary">

    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="text-center mb-4">
                <span class="fs-2 fw-bold text-gradient" style="font-family: 'Outfit', sans-serif;">AuraEvents</span>
                <p class="text-muted mt-2">Secure Administrative Authentication</p>
            </div>
            
            <form action="" method="POST">
                
                <div class="mb-3">
                    <label for="username" class="form-label text-white">Admin Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary border-secondary text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control border-secondary text-white" id="username" name="username" placeholder="Enter username" required autocomplete="off">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label text-white">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary border-secondary text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control border-secondary text-white" id="password" name="password" placeholder="Enter password" required autocomplete="off">
                    </div>
                </div>
                
                <button type="submit" class="btn-accent w-100 py-2">Authenticate Login</button>
                
            </form>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left"></i> Return to Main Site</a>
            </div>
            
        </div>
    </div>

    <!-- jQuery & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if (!empty($errorMsg)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Authentication Denied',
                text: '<?php echo $errorMsg; ?>',
                confirmButtonColor: '#a855f7'
            });
        </script>
    <?php endif; ?>
</body>
</html>
