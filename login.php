<?php
session_start();

// Secure Admin Authentication
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Remove extra spaces if accidentally typed
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Hardcoded secure admin credentials
    $admin_email = 'zeeljivani3101@gmail.com';
    $admin_pass = 'zeel123';

    if ($email === $admin_email && $password === $admin_pass) {
        // Successful login
        $_SESSION['user_id'] = 1;
        $_SESSION['user_email'] = $email;
        header("Location: admin/dashbord.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | PartyPlot</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .split-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
            background: var(--background);
        }
        .split-left {
            flex: 1;
            position: relative;
            background: url('https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=2070&auto=format&fit=crop') no-repeat center center/cover;
            display: none;
        }
        @media (min-width: 900px) {
            .split-left { display: block; }
        }
        .split-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(193, 154, 107, 0.4) 100%);
            /* Prevents the overlay from blocking anything */
            pointer-events: none; 
        }
        .left-content {
            position: absolute;
            bottom: 4rem;
            left: 4rem;
            color: white;
            z-index: 1;
            max-width: 500px;
        }
        .left-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: white;
            line-height: 1.1;
        }
        .left-content p {
            font-size: 1.2rem;
            font-weight: 300;
            opacity: 0.9;
        }
        
        .split-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
        }
        .login-box {
            width: 100%;
            max-width: 440px;
            background: var(--surface);
            padding: 3rem 2.5rem;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            position: relative;
            z-index: 10; /* Ensures the form is clickable */
        }
        .login-header {
            margin-bottom: 2.5rem;
            text-align: center;
        }
        .login-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: var(--secondary);
        }
        .login-header p {
            color: var(--text-muted);
        }
        .brand-logo {
            position: absolute;
            top: 2rem;
            right: 3rem;
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .brand-logo i { color: var(--primary); }
        
        .form-label {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .form-control {
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--background);
            color: var(--text-main);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(193, 154, 107, 0.2);
            background: var(--surface);
        }
        .btn-login {
            width: 100%;
            padding: 1.2rem;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
        }
        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
    </style>
</head>
<body>

    <div class="split-layout">
        <div class="split-left">
            <div class="left-content">
                <h1>Manage Your Legacy.</h1>
                <p>Welcome to the Royal Party Plot management portal. Access bookings, manage events, and curate unforgettable experiences.</p>
            </div>
        </div>
        
        <div class="split-right">
            <a href="index.php" class="brand-logo">
                <i class='bx bxs-party'></i> PartyPlot
            </a>
            
            <div class="login-box">
                <div class="login-header">
                    <h2>Admin Portal</h2>
                    <p>Sign in to manage your events and bookings.</p>
                </div>
                
                <?php if ($error): ?>
                    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #DC2626; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                        <i class='bx bx-error-circle'></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div>
                        <label class="form-label" for="email">Admin Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="admin@partyplot.com" required autofocus>
                    </div>
                    
                    <div>
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn-login">Access Dashboard</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Theme JS for dark mode check -->
    <script src="assets/js/theme.js"></script>
</body>
</html>
