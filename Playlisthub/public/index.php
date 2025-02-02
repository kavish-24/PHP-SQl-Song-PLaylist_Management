<?php
ob_start(); // Start output buffering
session_start();
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

$message = '';
$songs = []; 
$playlists = [];
$playlist_id = null;

// Handle playlist creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_playlist'])) {
    $playlist_name = trim($_POST['playlist_name']);
    
    if (!empty($playlist_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO Playlists (user_id, title) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $playlist_name]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error creating playlist: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $message = "Please fill in all required fields for the playlist.";
    }
}

// Handle playlist deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_playlist'])) {
    $delete_playlist_id = $_POST['delete_playlist_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Playlists WHERE playlist_id = ? AND user_id = ?");
        $stmt->execute([$delete_playlist_id, $_SESSION['user_id']]);
        $stmt = $pdo->prepare("DELETE FROM Playlist_Songs WHERE playlist_id = ?");
        $stmt->execute([$delete_playlist_id]);
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $message = "Error deleting playlist: " . htmlspecialchars($e->getMessage());
    }
}

// Fetch playlists
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'title';
$order = $_GET['order'] ?? 'ASC';

try {
    $query = "SELECT * FROM Playlists WHERE user_id = ?";
    if (!empty($search)) {
        $query .= " AND title LIKE ?";
    }
    $query .= " ORDER BY title $order";
    $stmt = $pdo->prepare($query);
    $stmt->execute(!empty($search) ? [$_SESSION['user_id'], "%$search%"] : [$_SESSION['user_id']]);
    $playlists = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error fetching playlists: " . htmlspecialchars($e->getMessage());
}

// Fetch recently added songs
try {
    $stmt = $pdo->prepare("SELECT * FROM Songs LIMIT 5"); // Adjust the query as needed
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
    <title>Playlist Hub - Desktop View</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<header></header>


<div class="desktop-container">
   <!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h3>Your Playlists</h3>

    <!-- Create New Playlist Button -->
    <button class="create-playlist-btn-icon" onclick="openModal()">
    <i class="bi bi-plus-lg"></i> Create New Playlist
    </button>

    <?php if ($playlists): ?>
        <table class="playlist-table">
            <thead>
                <!-- You can add headers here if needed -->
            </thead>
            <tbody>
                <?php foreach ($playlists as $pl): ?>
                    <tr>
                        <td>
                            <a href="showplaylist.php?playlist_id=<?php echo htmlspecialchars($pl['playlist_id']); ?>" class="playlist-link">
                                <i class="fa fa-music" aria-hidden="true" style="margin-right: 8px; color: #17a2b8;"></i>
                                <?php echo htmlspecialchars($pl['title']); ?>
                            </a>
                        </td>
                        <td>
                            <!-- Delete Button -->
                            <form method="post" class="delete-form" style="display:inline;">
                                <input type="hidden" name="delete_playlist_id" value="<?php echo htmlspecialchars($pl['playlist_id']); ?>">
                                <button type="submit" name="delete_playlist" class="delete-btn" onclick="return confirm('Are you sure you want to delete this playlist?');">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>

                            <!-- Add Songs Button -->
                            <a href="search.php" class="update-btn">
                                <i class="bi bi-plus-circle"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-playlists">No playlists found. Create one!</p>
    <?php endif; ?>
</div>


<!-- Modal Structure -->
<div id="createPlaylistModal" class="modal create-playlist-modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Create New Playlist</h2>
        <form method="post" class="form-create">
            <input type="text" name="playlist_name" placeholder="Playlist Name" required>
            <button type="submit" name="create_playlist" class="create-playlist-btn-modal"> Create Playlist</button>
        </form>
    </div>
</div>


    

    <!-- Sidebar Toggle Button -->
    <button id="toggleButton" class="sidebar-toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Content -->
    <div class="content">
        
    <form method="get" class="form-search">
    <div class="search-container">
        <i class="fas fa-search search-icon"></i> <!-- Font Awesome Search Icon -->
        <input type="text" name="search" placeholder="Search Playlist" value="<?php echo htmlspecialchars($search); ?>">
    </div>
</form>







        <h3>Recently Added Songs</h3>
        <table class="songs-table">
            <thead>
                <tr>
                    <th>Song Title</th>
                    <th>Artist</th>
                    <th>Album</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($songs as $song): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($song['title']); ?></td>
                        <td><?php echo htmlspecialchars($song['artist']); ?></td>
                        <td><?php echo htmlspecialchars($song['album']); ?></td>
                        <td>
                            <i class="fas fa-headphones" onclick="showDetails('<?php echo htmlspecialchars($song['title']); ?>', '<?php echo htmlspecialchars($song['artist']); ?>', '<?php echo htmlspecialchars($song['album']); ?>')"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeDetailsModal()">&times;</span>
        <i class="fas fa-headphones"></i>
        <h2>Song Details</h2>
        <p id="songDetails"></p>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('toggleButton');
    sidebar.classList.toggle('collapsed');
    
    // Check if sidebar is collapsed and adjust the toggle button visibility
    if (sidebar.classList.contains('collapsed')) {
        toggleButton.style.display = 'block'; // Button becomes visible when sidebar is collapsed
    }
}

function showDetails(title, artist, album) {
    const details = `Title: ${title}<br>Artist: ${artist}<br>Album: ${album}`;
    document.getElementById('songDetails').innerHTML = details;
    document.getElementById('detailsModal').style.display = 'block';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

// Open modal
function openModal() {
    document.getElementById("createPlaylistModal").style.display = "block";
}

// Close modal
function closeModal() {
    document.getElementById("createPlaylistModal").style.display = "none";
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    var modal = document.getElementById("createPlaylistModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

</script>

</body>
</html>
