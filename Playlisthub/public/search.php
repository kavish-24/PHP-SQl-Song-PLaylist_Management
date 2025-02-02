<?php
// Database connection
$host = 'localhost';
$db = 'playlisthub';
$user = 'root';
$pass = ''; // Update password as per your setup
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include 'header.php';

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['song_id'], $_POST['playlist_id'])) {
        $songId = $conn->real_escape_string((int)$_POST['song_id']);
        $playlistId = $conn->real_escape_string((int)$_POST['playlist_id']);

        // Check if the song exists in the songs table
        $songCheckQuery = "SELECT * FROM songs WHERE song_id = '$songId'";
        $songCheckResult = $conn->query($songCheckQuery);

        if ($songCheckResult && $songCheckResult->num_rows === 0) {
            // Song does not exist in the songs table
            echo "<script>alert('The selected song does not exist.');</script>";
        } else {
            // Check if the playlist exists in the playlists table
            $playlistCheckQuery = "SELECT * FROM playlists WHERE playlist_id = '$playlistId'";
            $playlistCheckResult = $conn->query($playlistCheckQuery);

            if ($playlistCheckResult && $playlistCheckResult->num_rows === 0) {
                // Playlist does not exist
                echo "<script>alert('The selected playlist does not exist.');</script>";
            } else {
                // Check if the song already exists in the playlist
                $checkQuery = "SELECT * FROM playlist_songs WHERE playlist_id = '$playlistId' AND song_id = '$songId'";
                $checkResult = $conn->query($checkQuery);

                if ($checkResult && $checkResult->num_rows > 0) {
                    echo "<script>alert('This song is already in the playlist.');</script>";
                } else {
                    // Insert the song into the playlist using existing playlist_id and song_id
                    $query = "INSERT INTO playlist_songs (playlist_id, song_id) VALUES ('$playlistId', '$songId')";
                    if ($conn->query($query)) {
                        echo "<script>alert('Song added successfully!');</script>";
                    } else {
                        echo "<script>alert('Error adding song to playlist: " . $conn->error . "');</script>";
                    }
                }
            }
        }
    }
}






// Fetch genres dynamically from the database
$genres = [];
$genreResult = $conn->query("SELECT DISTINCT genre FROM songs WHERE genre IS NOT NULL");

if ($genreResult && $genreResult->num_rows > 0) {
    while ($row = $genreResult->fetch_assoc()) {
        $genres[] = $row['genre'];
    }
}

// Handle search and filters
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$excludedGenres = isset($_GET['exclude_genre']) ? $_GET['exclude_genre'] : [];

$query = "SELECT songs.song_id, songs.title, songs.artist, songs.genre, songs.duration
          FROM songs
          WHERE 1";

// Search filter
if ($searchQuery) {
    $safeSearch = $conn->real_escape_string($searchQuery);
    $query .= " AND (songs.title LIKE '%$safeSearch%' OR songs.artist LIKE '%$safeSearch%')";
}

// Genre exclusion filter
if (!empty($excludedGenres)) {
    $excludedGenresList = implode(',', array_map(function($genre) use ($conn) {
        return "'" . $conn->real_escape_string($genre) . "'";
    }, $excludedGenres));
    $query .= " AND songs.genre  IN ($excludedGenresList)";
}

$result = $conn->query($query);

// Fetch songs
$songs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Songs</title>
    <link rel="stylesheet" href="../assets/css/style7.css"> <!-- Add your CSS file -->
</head>
<body>
    <div class="container">
    <header class="search-header">
    <form action="" method="GET" class="search-form">
        <input 
            type="text" 
            name="search" 
            placeholder="Search by title or artist..." 
            value="<?= htmlspecialchars($searchQuery) ?>" 
            class="search-input">
        <button type="submit" class="search-button">
             <i class="fas fa-search search-icon"></i>
        </button>
    </form>

    <!-- Genre Tabs -->
    <div id="buttons">
    <button class="button-value" onclick="filterSongs('all', event)">All</button>
    <button class="button-value" onclick="filterSongs('Pop', event)">Pop</button>
    <button class="button-value" onclick="filterSongs('Synthwave', event)">Synthwave</button>
    <button class="button-value" onclick="filterSongs('Rock', event)">Rock</button>
    <button class="button-value" onclick="filterSongs('Hip-Hop', event)">Hip-Hop</button>
    <button class="button-value" onclick="filterSongs('Jazz', event)">Jazz</button>
    <button class="button-value" onclick="filterSongs('Country', event)">Country</button>
    <button class="button-value" onclick="filterSongs('Electronic', event)">Electronic</button>
</div>




</header>







<main class="results">
    <ul>
        <?php if (!empty($songs)): ?>
            <?php foreach ($songs as $song): ?>
                <li class="song-card" data-genre="<?= htmlspecialchars($song['genre']) ?>">
                    <div class="song-details">
                        <h3 class="song-title"><?= htmlspecialchars($song['title']) ?></h3>
                        <p class="song-artist"><?= htmlspecialchars($song['artist']) ?></p>
                        <p class="song-duration"><?= htmlspecialchars($song['duration']) ?></p>
                    </div>
                    <button 
                        class="add-to-playlist" 
                        data-song-id="<?= htmlspecialchars($song['song_id']) ?>" 
                        onclick="openPlaylistModal(<?= htmlspecialchars($song['song_id']) ?>)">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="no-results">No results found. Try a different search!</li>
        <?php endif; ?>
    </ul>
</main>


<div id="playlistModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePlaylistModal()">&times;</span>
        <h2>Select Playlist</h2>
        <form id="addToPlaylistForm" action="search.php" method="POST">
    <input type="hidden" name="song_id" id="songIdInput">
    <select name="playlist_id" id="playlistSelect" required>
        <option value="">--Select Playlist--</option>
        <?php
        $userId = $_SESSION['user_id']; // Retrieve logged-in user's ID
        $playlists = $conn->query("SELECT * FROM playlists WHERE user_id = '$userId'");
        if ($playlists && $playlists->num_rows > 0) {
            while ($playlist = $playlists->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($playlist['playlist_id']) . "'>" . htmlspecialchars($playlist['title']) . "</option>";
            }
        } else {
            echo "<option value=''>No playlists available</option>";
        }
        ?>
    </select>
    <button type="submit">Add to Playlist</button>
</form>

    </div>
</div>


<script>
    function openPlaylistModal(songId) {
    const modal = document.getElementById('playlistModal');
    const songIdInput = document.getElementById('songIdInput');
    modal.style.display = 'flex';
    songIdInput.value = songId;
}

function closePlaylistModal() {
    const modal = document.getElementById('playlistModal');
    modal.style.display = 'none';
}
</script>
<script>
    function filterSongs(genre) {
    const songCards = document.querySelectorAll('.song-card');

    songCards.forEach(card => {
        const cardGenre = card.getAttribute('data-genre');

        if (genre === 'all' || cardGenre === genre) {
            card.style.display = 'block'; // Show the card
        } else {
            card.style.display = 'none'; // Hide the card
        }
    });
}

</script>
</body>
</html>
