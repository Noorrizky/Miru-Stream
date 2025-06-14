<?php
// admin/login.php
session_start();
require_once '../includes/config.php'; // Path relative to admin folder

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                // Verify the hashed password
                if (password_verify($password, $user['password'])) {
                    // Password is correct, set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];

                    // Check if the user has the 'admin' role
                    if ($user['role'] === 'admin') {
                        header('Location: index.php');
                        exit();
                    } else {
                        $error = 'You do not have administrative privileges.';
                        session_destroy(); // Destroy session if not admin
                    }
                } else {
                    $error = 'Invalid username or password.';
                }
            } else {
                $error = 'Invalid username or password.';
            }
            $stmt->close();
        } else {
            $error = 'Database error: Could not prepare statement.';
        }
    }
}
// Close connection for this standalone script
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    </head>
<body class="d-flex flex-column min-vh-100"> <div class="container my-auto"> <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4"> <div class="card shadow-lg">
                    <div class="card-header text-center py-3" style="background-color: var(--nijika-yellow-bright); color: var(--text-dark-contrast);">
                        <h3 class="mb-0 fs-4 fw-bold" style="font-family: 'Open Sans ExtraBold', sans-serif;">Admin Login</h3> </div>
                    <div class="card-body p-4" style="background-color: var(--dark-secondary);"> <?php if ($error): ?>
                            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label" style="color: var(--text-light);">Username</label> <input type="text" class="form-control" id="username" name="username" required autocomplete="username" style="background-color: var(--dark-background); color: var(--text-light); border-color: var(--border-dark);">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label" style="color: var(--text-light);">Password</label> <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" style="background-color: var(--dark-background); color: var(--text-light); border-color: var(--border-dark);">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3" style="background-color: var(--dark-secondary); border-top: 1px solid var(--border-dark);">
                        <small style="color: var(--text-muted-dark);">© <?php echo date('Y'); ?> Miru</small>
                        <br>
                        <a href="../index.php" class="btn btn-link mt-2" style="color: var(--nijika-yellow-light);">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>