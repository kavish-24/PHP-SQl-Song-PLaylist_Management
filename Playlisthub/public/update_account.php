<?php
session_start();
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

$message = '';
$success = '';

$stmt = $pdo->prepare("SELECT username, email FROM Users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) as playlist_count FROM playlists WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$playlist_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as song_count FROM playlist_songs WHERE playlist_id IN (SELECT playlist_id FROM playlists WHERE user_id = ?)");
$stmt->execute([$_SESSION['user_id']]);
$song_count = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (!empty($username) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ? AND user_id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $message = "Email is already in use. Please choose another.";
            } else {
                $stmt = $pdo->prepare("UPDATE Users SET username = ?, email = ? WHERE user_id = ?");
                $stmt->execute([$username, $email, $_SESSION['user_id']]);
                $success = "Your account details have been updated successfully!";
                header("Location: update_account.php");
                exit;
            }
        } catch (PDOException $e) {
            $message = "Error updating account: Account already exists" ;
        }
    } else {
        $message = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Update Your Account Details</h2>

        <?php if ($message): ?>
            <div class="error-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <p>Number of Playlists Created: <?php echo htmlspecialchars($playlist_count); ?></p>
        <p>Number of Songs Added: <?php echo htmlspecialchars($song_count); ?></p>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>