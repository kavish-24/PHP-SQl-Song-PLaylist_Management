<?php
session_start();

// Admin login check
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Include database connection
require_once '../config/db.php';

// Search logic
$search = $_GET['search'] ?? '';
$searchCondition = $search ? "WHERE u.username LIKE :search" : '';

// Fetch users and their playlists
$sql = "
    SELECT u.user_id, u.username, p.playlist_id, p.title
    FROM users u
    LEFT JOIN playlists p ON u.user_id = p.user_id
    $searchCondition
    ORDER BY u.username, p.title
";
try {
    $stmt = $pdo->prepare($sql);
    if ($search) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[$row['user_id']]['username'] = $row['username'];
        $users[$row['user_id']]['playlists'][] = [
            'playlist_id' => $row['playlist_id'],
            'title' => $row['title'],
            'songs' => []
        ];
    }
} catch (PDOException $e) {
    die("Query failed: " . htmlspecialchars($e->getMessage()));
}

// Fetch songs for each playlist
foreach ($users as &$user) {
    foreach ($user['playlists'] as &$playlist) {
        if ($playlist['playlist_id']) {
            $stmt = $pdo->prepare("
                SELECT s.song_id, s.title, s.artist, s.album, s.genre, s.duration 
                FROM songs s 
                JOIN playlist_songs ps ON s.song_id = ps.song_id 
                WHERE ps.playlist_id = :playlist_id
            ");
            $stmt->execute(['playlist_id' => $playlist['playlist_id']]);
            $playlist['songs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/adstyle.css">
</head>
<body>
    
    <div class="container">
        <header>
            <h2>Admin Dashboard</h2>
        </header>

        <!-- Add Songs Button -->
<div class="add-songs-button">
    <a href="playlists.php" class="add-songs-btn">Add Songs</a>
</div>

<div class="view-songs-button">
    <a href="songs.php" class="view-songs-btn">View Songs</a>
</div>


        <!-- Search Form -->
        <section class="search-section">
            <form method="get" action="admin.php">
                <label for="search">Search Users:</label>
                <input type="text" id="search" name="search" placeholder="Enter username" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </section>

        <!-- Display Users and Playlists -->
        <section class="user-playlists-section">
            <h3>Users and Their Playlists</h3>

            <?php if (empty($users)): ?>
                <p class="no-users">No users found.</p>
            <?php else: ?>
                <table class="user-playlist-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Number of Playlists</th>
                            <th>Playlists</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $userId => $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($userId); ?></td>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo count($user['playlists']); ?></td>
                                <td>
                                    <?php if (empty($user['playlists'])): ?>
                                        No playlists
                                    <?php else: ?>
                                        <ul>
                                            <?php foreach ($user['playlists'] as $playlist): ?>
                                                <li>
                                                    <a href="javascript:void(0);" onclick="toggleSongList(<?php echo $playlist['playlist_id']; ?>)">
                                                        <?php echo htmlspecialchars($playlist['title']); ?>
                                                    </a>
                                                    <ul id="songs-<?php echo $playlist['playlist_id']; ?>" style="display:none;">
                                                        <?php if (!empty($playlist['songs'])): ?>
                                                            <?php foreach ($playlist['songs'] as $song): ?>
                                                                <li>
                                                                    <a href="javascript:void(0);" onclick="showSongPopup('<?php echo $song['song_id']; ?>', '<?php echo htmlspecialchars($song['title']); ?>', '<?php echo htmlspecialchars($song['artist']); ?>', '<?php echo htmlspecialchars($song['album']); ?>', '<?php echo htmlspecialchars($song['genre']); ?>', '<?php echo htmlspecialchars($song['duration']); ?>')">
                                                                        <?php echo htmlspecialchars($song['title']); ?>
                                                                    </a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <li>No songs in this playlist</li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <!-- Song Details Popup Modal -->
        <div id="song-popup" class="popup">
            <div class="popup-content">
                <span class="close" onclick="closePopup()">&times;</span>
                <h3>Song Details</h3>
                <form id="song-edit-form" method="post" action="admin.php">
                    <input type="hidden" name="song_id" id="popup-song-id">
                    <label for="popup-title">Title:</label>
                    <input type="text" name="title" id="popup-title">
                    <label for="popup-artist">Artist:</label>
                    <input type="text" name="artist" id="popup-artist">
                    <label for="popup-album">Album:</label>
                    <input type="text" name="album" id="popup-album">
                    <label for="popup-genre">Genre:</label>
                    <input type="text" name="genre" id="popup-genre">
                    <label for="popup-duration">Duration:</label>
                    <input type="text" name="duration" id="popup-duration">
                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSongList(playlistId) {
            const songList = document.getElementById('songs-' + playlistId);
            songList.style.display = songList.style.display === 'none' ? 'block' : 'none';
        }

        function showSongPopup(songId, title, artist, album, genre, duration) {
            document.getElementById('popup-song-id').value = songId;
            document.getElementById('popup-title').value = title;
            document.getElementById('popup-artist').value = artist;
            document.getElementById('popup-album').value = album;
            document.getElementById('popup-genre').value = genre;
            document.getElementById('popup-duration').value = duration;
            document.getElementById('song-popup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('song-popup').style.display = 'none';
        }
    </script>

</body>
</html>
