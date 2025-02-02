<?php
ob_start(); // Start output buffering
session_start();
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once '../config/db.php';

// Check if playlist_id is set in the URL
if (isset($_GET['playlist_id'])) {
    $playlist_id = intval($_GET['playlist_id']);

    // Remove song from playlist logic
    if (isset($_GET['remove_song_id'])) {
        $song_id = intval($_GET['remove_song_id']);
        
        // SQL to remove song from playlist
        $remove_query = "DELETE FROM playlist_songs WHERE playlist_id = :playlist_id AND song_id = :song_id";
        $remove_stmt = $pdo->prepare($remove_query);
        $remove_stmt->execute(['playlist_id' => $playlist_id, 'song_id' => $song_id]);

        // Redirect to avoid re-submission
        header("Location: showplaylist.php?playlist_id=" . $playlist_id);
        exit;  // Ensure the script stops after the redirect
    }

    // Prepare the SQL query to get the playlist details and songs
    $query = "
        SELECT 
            playlists.title AS playlist_title,
            songs.song_id,
            songs.title AS song_title,
            songs.artist AS song_artist,
            songs.duration AS song_duration
        FROM 
            playlists
        JOIN 
            playlist_songs ON playlists.playlist_id = playlist_songs.playlist_id
        JOIN 
            songs ON playlist_songs.song_id = songs.song_id
        WHERE 
            playlists.playlist_id = :playlist_id
    ";

    // Prepare and execute the statement using PDO
    $stmt = $pdo->prepare($query);
    $stmt->execute(['playlist_id' => $playlist_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="container">';

    // Check if there are any results
    if (count($results) > 0) {
        // Fetch the playlist title from the first row
        echo "<h1 class='playlist-title'>" . htmlspecialchars($results[0]['playlist_title']) . "</h1>";
        
        echo "<ul class='song-list'>";
        foreach ($results as $row) {
            echo "<li class='song-item'>";
            echo "<div class='song-details'>";
            echo "<span class='song-title'>" . htmlspecialchars($row['song_title']) . "</span>";
            echo "<span class='song-artist'>" . htmlspecialchars($row['song_artist']) . "</span>";
            echo "<span class='song-duration'>" . htmlspecialchars($row['song_duration']) . "</span>";
            echo "</div>";

            // Three dot menu for each song
            echo "<div class='song-dropdown'>
            <button class='song-dropdown-btn' onclick='toggleDropdown(" . $row['song_id'] . ")'>&#8230;</button>
                <div class='song-dropdown-menu' id='dropdown-" . $row['song_id'] . "'>
                    <a href='#' class='song-dropdown-item' onclick='viewSongDetails(" . $row['song_id'] . ")'>View Details</a>
                    <a href='?playlist_id=" . $playlist_id . "&remove_song_id=" . $row['song_id'] . "' class='song-dropdown-item'>Remove from Playlist</a>
                </div>
            </div>";


            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='no-songs'>No songs found in this playlist.</p>";
    }

    echo '</div>';
} else {
    echo "<p class='error-message'>No playlist selected.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playlist Songs</title>
    <link rel="stylesheet" href="../assets/css/style8.css">
</head>
<body>
    

    <script>
function toggleDropdown(songId) {
    // Get all dropdown menus
    const allDropdowns = document.querySelectorAll('.song-dropdown-menu');
    
    // Close all dropdowns except the one being toggled
    allDropdowns.forEach(dropdown => {
        if (dropdown.id !== 'dropdown-' + songId) {
            dropdown.classList.remove('show');
        }
    });

    // Toggle the clicked dropdown
    const dropdownMenu = document.getElementById('dropdown-' + songId);
    dropdownMenu.classList.toggle('show');
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    // Check if the clicked element is not a button or inside a dropdown
    if (!event.target.matches('.song-dropdown-btn')) {
        const allDropdowns = document.querySelectorAll('.song-dropdown-menu');
        allDropdowns.forEach(dropdown => {
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show'); // Hide the dropdown
            }
        });
    }
};

function viewSongDetails(songId) {
    alert('View details for song ID: ' + songId); // Implement logic to show song details
}
</script>


</body>
</html>
