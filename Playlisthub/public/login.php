<?php
session_start();

// Include database connection
require_once '../config/db.php';

// Initialize variables for error messages
$message = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user credentials from form input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (!empty($username) && !empty($password)) {
        try {
            // Prepare SQL statement to fetch user data based on username
            $stmt = $pdo->prepare("SELECT user_id, password FROM Users WHERE username = ?");
            $stmt->execute([$username]);
            
            // Fetch user data from database
            if ($user = $stmt->fetch()) {
                // Verify password using password_verify()
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];  // Store user ID in session

                    header("Location: index.php");  // Redirect to index page after successful login
                    exit;
                } else {
                    $message = "Invalid username or password.";
                }
            } else {
                $message = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            // Handle any errors that occur during database operations
            $message = "Database error: " . htmlspecialchars($e->getMessage());
        }
    } else {
        // Error message for empty fields
        $message = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Playlist Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style3.css"> <!-- Link to your custom CSS -->
</head>
<body class="relative h-screen bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">
    <div class="absolute inset-0 bg-black opacity-50"></div> <!-- Semi-transparent overlay -->

    <!-- Login Form -->
    <div class="min-h-screen flex items-center justify-center relative z-10">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-sm">
            <h2 class="text-3xl font-semibold text-center text-indigo-600 mb-6">Login</h2>

            <?php if ($message): ?>
                <div class="bg-red-500 text-white p-3 rounded mb-4 text-center"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <input type="text" name="username" placeholder="Username" required autofocus class="w-full p-4 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password" required class="w-full p-4 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500" />
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg hover:bg-indigo-700 focus:outline-none transition duration-200 transform hover:scale-105">Login</button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">Don't have an account? <a href="register.php" class="text-indigo-600 hover:underline">Register here!</a></p>
            </div>
            <div class="mt-2 text-center">
                <p class="text-sm text-gray-600">Admin login? <a href="admin_login.php" class="text-indigo-600 hover:underline">Click here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
