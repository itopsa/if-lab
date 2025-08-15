<?php
// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ?page=admin');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - IF Bowling Club</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            padding: 2rem 1.5rem;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .back-link {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-lock me-2"></i>Admin Login
                    </h4>
                    <p class="mb-0 mt-2">IF Bowling Club</p>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($login_error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($login_error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="?page=dashboard" class="back-link">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <small class="text-white">
                    <i class="fas fa-shield-alt me-1"></i>Secure Admin Access
                </small>
            </div>
        </div>
    </div>
</body>
</html>
