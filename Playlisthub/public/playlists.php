<?php
session_start();
 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Include database connection
require_once '../config/db.php';

// Initialize variables
$message = '';
$songs = [];

// Check if we are adding a new song
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_song'])) {
    if (isset($_POST['song_title'], $_POST['artist'], $_POST['duration'])) {
        $song_title = trim($_POST['song_title']);
        $artist = trim($_POST['artist']);
        $album = $_POST['album'] ?? null; // Optional field
        $genre = $_POST['genre'] ?? null; // Optional field
        $duration = $_POST['duration'];

        try {
            // Insert the new song into the songs table
            $stmt = $pdo->prepare("INSERT INTO songs (title, artist, album, genre, duration) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$song_title, $artist, $album, $genre, $duration]);
            $message = "Song added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding song: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $message = "Please fill in all required fields for the song.";
    }
}

// Fetch all songs
try {
    $stmt = $pdo->prepare("SELECT * FROM songs");
    $stmt->execute();
    $songs = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error fetching songs: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Music</title>
    <link rel="stylesheet" href="../assets/css/stylep.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="content">
    <h2>Add Music</h2>

    <?php if ($message): ?>
        <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Form for adding a song -->
    <form method="post">
        <input type="text" name="song_title" placeholder="Song Title" required>
        <input type="text" name="artist" placeholder="Artist" required>
        <input type="text" name="album" placeholder="Album">
        
        <label for="genre">Genre:</label>
        <select name="genre" id="genre">
            <option value="" disabled selected>Select a genre</option>
            <option value="Pop">Pop</option>
            <option value="Rock">Rock</option>
            <option value="Hip-Hop">Hip-Hop</option>
            <option value="Jazz">Jazz</option>
            <option value="Classical">Classical</option>
            <option value="Electronic">Electronic</option>
            <option value="Reggae">Reggae</option>
            <option value="Country">Country</option>
        </select>

        <input type="time" name="duration" placeholder="Duration" required>
        
        <button type="submit" name="add_song">Add Song</button>
    </form>

    <!-- Display all songs -->
    
</div>
</body>
</html>
