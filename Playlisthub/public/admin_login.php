<?php
session_start();

// Include database connection
require_once '../config/db.php';

$message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if fields are not empty
    if (!empty($username) && !empty($password)) {
        try {
            // Ensure $pdo is correctly set up in db.php
            if (isset($pdo)) {
                // Prepare SQL statement to fetch admin data based on username
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
                $stmt->execute([$username]);

                // Fetch the admin data
                $admin = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

                if ($admin) {
                    // Directly compare passwords without hashing
                    if ($password === $admin['password']) {
                        $_SESSION['admin_id'] = $admin['admin_id']; // Corrected variable name
                        header("Location:admin.php"); // Redirect on successful login
                        exit;
                    } else {
                        $message = "Incorrect username or password.";
                    }
                } else {
                    $message = "No admin account found with that username.";
                }
            } else {
                $message = "Database connection not available.";
            }
        } catch (PDOException $e) {
            $message = "Database error: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $message = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin</title>
    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-green-400 via-blue-500 to-purple-600">
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-semibold text-center mb-6">Login As Admin</h2>

        <?php if (!empty($message)): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-center"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" class="space-y-4">
            <div>
                <input type="text" name="username" placeholder="Username" required autofocus class="w-full p-3 border border-gray-300 rounded-lg" />
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required class="w-full p-3 border border-gray-300 rounded-lg" />
            </div>
            <button type="submit" class="w-full bg-green-500 text-white p-3 rounded-lg hover:bg-green-600 focus:outline-none">Login</button>
        </form>
    </div>
</div>
</body>
</html>
