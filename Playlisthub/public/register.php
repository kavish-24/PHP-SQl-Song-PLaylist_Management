<?php 
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the email already exists
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Email already exists! Please log in or use a different email.";
    } else {
        // Insert the new user
        $stmt = $pdo->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);

        // Redirect to home after successful registration
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-green-400 via-blue-500 to-purple-600">

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-semibold text-center mb-6">Create an Account</h2>

        <?php if (!empty($message)): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-center"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="POST" class="space-y-4">
            <div>
                <input type="text" name="username" placeholder="Username" required autofocus class="w-full p-3 border border-gray-300 rounded-lg" />
            </div>
            <div>
                <input type="email" name="email" placeholder="Email" required class="w-full p-3 border border-gray-300 rounded-lg" />
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required class="w-full p-3 border border-gray-300 rounded-lg" />
            </div>
            <button type="submit" class="w-full bg-green-500 text-white p-3 rounded-lg hover:bg-green-600 focus:outline-none">Register</button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-sm">Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Login here</a></p>
        </div>
    </div>
</div>

</body>
</html>
