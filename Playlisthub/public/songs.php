<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once '../config/db.php';

// Initialize variables
$song_name_filter = isset($_GET['song_name']) ? $_GET['song_name'] : '';
$playlists = [];
$genre_filter = $_GET['genre'] ?? '';
$playlist_filter = $_GET['playlist_id'] ?? '';

// Fetch all playlists for the logged-in user
try {
    $stmt = $pdo->prepare("SELECT * FROM Playlists WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $playlists = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error fetching playlists: " . htmlspecialchars($e->getMessage());
}

// Fetch songs based on selected filters
try {
  $query = "SELECT song_id, title, artist, genre, album, duration 
  FROM songs 
  WHERE 1";

$params = [];

if (!empty($song_name_filter)) {
$query .= " AND title LIKE ?";
$params[] = '%' . $song_name_filter . '%';
}

if (!empty($genre_filter)) {
$query .= " AND genre = ?";
$params[] = $genre_filter;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$songs = $stmt->fetchAll();

} catch (PDOException $e) {
    $message = "Error fetching songs: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Songs</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
      body {
        font-family: 'Century Schoolbook', serif;
        background: linear-gradient(to bottom right, #ffffff, #e9ecef);
        margin: 0;
        padding: 20px;
      }

      header {
        text-align: center;
        margin-bottom: 20px;
      }

      h1 {
        color: #007bff;
        font-size: 36px;
        margin-bottom: 10px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
      }

      .content {
        background: #ffffff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      }

      .form-search {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 15px;
        align-items: center;
        margin-bottom: 30px;
      }

      .form-search input,
      .form-search select,
      .form-search button {
        padding: 12px 15px;
        border-radius: 30px;
        border: 1px solid #ccc;
        font-size: 16px;
        transition: all 0.3s ease-in-out;
      }

      .form-search input,
      .form-search select {
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
      }

      .form-search input:focus,
      .form-search select:focus {
        border-color: #007bff;
        outline: none;
      }

      .form-search button {
        background: linear-gradient(to right, #007bff, #0056b3);
        color: white;
        border: none;
        cursor: pointer;
        font-weight: bold;
        text-transform: uppercase;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      }

      .form-search button:hover {
        background: linear-gradient(to right, #0056b3, #003a75);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
      }

      .song-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 16px;
      }

      .song-table th,
      .song-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
      }

      .song-table th {
        background-color: #007bff;
        color: #fff;
        text-transform: uppercase;
      }

      .song-table tr:hover {
        background-color: #f1f1f1;
      }

      .delete-btn,
      .update-btn {
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
      }

      .update-btn {
        background: #28a745;
        color: white;
      }

      .update-btn:hover {
        background: #218838;
      }

      .delete-btn {
        background: #dc3545;
        color: white;
      }

      .delete-btn:hover {
        background: #c82333;
      }

      .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        justify-content: center;
        align-items: center;
      }

      .modal-content {
        background-color: #fff;
        padding: 25px;
        border-radius: 15px;
        width: 350px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      }

      .modal input {
        width: calc(100% - 20px);
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 20px;
        border: 1px solid #ddd;
      }

      .modal button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(to right, #007bff, #0056b3);
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
      }

      .modal button:hover {
        background: linear-gradient(to right, #0056b3, #003a75);
      }
 </style>

</head>

<body>
<div class="desktop-container">
    <header>
        <h1>All Songs</h1>
    </header>

    <div class="content">
        <h3>Filter Songs</h3>
        <form method="get" class="form-search">
            <input type="text" name="song_name" placeholder="Search by Song Name" value="<?php echo htmlspecialchars($song_name_filter); ?>">
            <select name="genre">
                <option value="">Select Genre</option>
                <option value="Pop" <?php echo ($genre_filter == 'Pop') ? 'selected' : ''; ?>>Pop</option>
                <option value="Rock" <?php echo ($genre_filter == 'Rock') ? 'selected' : ''; ?>>Rock</option>
                <option value="Hip-Hop" <?php echo ($genre_filter == 'Hip-Hop') ? 'selected' : ''; ?>>Hip-Hop</option>
                <option value="Jazz" <?php echo ($genre_filter == 'Jazz') ? 'selected' : ''; ?>>Jazz</option>
                <option value="Classical" <?php echo ($genre_filter == 'Classical') ? 'selected' : ''; ?>>Classical</option>
                <option value="Electronic" <?php echo ($genre_filter == 'Electronic') ? 'selected' : ''; ?>>Electronic</option>
                <option value="Reggae" <?php echo ($genre_filter == 'Reggae') ? 'selected' : ''; ?>>Reggae</option>
                <option value="Country" <?php echo ($genre_filter == 'Country') ? 'selected' : ''; ?>>Country</option>
            </select>
            
            <button type="submit">Apply Filters</button>
        </form>

        <h3>All Songs</h3>
        <table class="song-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Artist</th>
            <th>Genre</th>
            <th>Album</th>
            <th>Duration</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($songs): ?>
            <?php foreach ($songs as $song): ?>
                <tr data-song-id="<?php echo $song['song_id']; ?>">
                    <td><?php echo htmlspecialchars($song['title']); ?></td>
                    <td><?php echo htmlspecialchars($song['artist']); ?></td>
                    <td><?php echo htmlspecialchars($song['genre']); ?></td>
                    <td><?php echo htmlspecialchars($song['album']); ?></td>
                    <td><?php echo htmlspecialchars($song['duration']); ?></td>
                    <td>
                        <button class="update-btn" onclick="openModal(<?php echo $song['song_id']; ?>, '<?php echo htmlspecialchars($song['title']); ?>', '<?php echo htmlspecialchars($song['artist']); ?>', '<?php echo htmlspecialchars($song['genre']); ?>')">Update</button>
                        <button class="delete-btn" onclick="deleteSong(<?php echo $song['song_id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No songs found for the selected filters.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


        <div class="modal" id="updateModal">
            <div class="modal-content">
                <h4>Update Song</h4>
                <input type="text" id="newTitle" placeholder="New Title">
                <input type="text" id="newArtist" placeholder="New Artist">
                <input type="text" id="newGenre" placeholder="New Genre">
                <button onclick="updateSongDetails()">Update</button>
                <button onclick="closeModal()">Cancel</button>
            </div>
        </ div>

<script>
    function openModal(songId, currentTitle, currentArtist, currentGenre) {
        document.getElementById("newTitle").value = currentTitle;
        document.getElementById("newArtist").value = currentArtist;
        document.getElementById("newGenre").value = currentGenre;
        document.getElementById("updateModal").setAttribute("data-song-id", songId);
        document.getElementById("updateModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("updateModal").style.display = "none";
    }

    function updateSongDetails() {
        var songId = document.getElementById("updateModal").getAttribute("data-song-id");
        var newTitle = document.getElementById("newTitle").value;
        var newArtist = document.getElementById("newArtist").value;
        var newGenre = document.getElementById("newGenre").value;

        $.ajax({
            url: "update_delete_song.php",
            method: "POST",
            data: {
                action: 'update',
                song_id: songId,
                title: newTitle,
                artist: newArtist,
                genre: newGenre
            },
            success: function(response) {
                if (response == "success") {
                    alert("Song updated successfully");
                    location.reload();
                } else {
                    alert("Error updating song");
                }
            }
        });

        closeModal();
    }

    function deleteSong(songId) {
        if (confirm("Are you sure you want to delete this song?")) {
            $.ajax({
                url: "update_delete_song.php",
                method: "POST",
                data: {
                    action: 'delete',
                    song_id: songId
                },
                success: function(response) {
                    if (response == "success") {
                        alert("Song deleted successfully");
                        location.reload();
                    } else {
                        alert("Error deleting song");
                    }
                }
            });
        }
    }
</script>
</div>
</body>
</html>