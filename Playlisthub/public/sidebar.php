<?php
// Predefined colors for icons
$colors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A6', '#FFC300', '#DAF7A6'];

foreach ($playlists as $index => $pl):
    // Get the first letter of the playlist name
    $firstLetter = strtoupper(substr($pl['title'], 0, 1));
    // Assign a background color from the array (cycle through colors)
    $bgColor = $colors[$index % count($colors)];
?>
<tr>
    <td>
        <!-- Icon with the first letter and dynamic background color -->
        <div class="playlist-icon" style="background-color: <?php echo $bgColor; ?>;">
            <?php echo htmlspecialchars($firstLetter); ?>
        </div>
        <a href="playlists.php?playlist_id=<?php echo htmlspecialchars($pl['playlist_id']); ?>" class="playlist-link">
            <?php echo htmlspecialchars($pl['title']); ?>
        </a>
    </td>
    <td>
        <!-- Delete Button -->
        <form method="post" class="delete-form" style="display:inline;">
            <input type="hidden" name="delete_playlist_id" value="<?php echo htmlspecialchars($pl['playlist_id']); ?>">
            <button type="submit" name="delete_playlist" class="delete-btn" onclick="return confirm('Are you sure you want to delete this playlist?');">
                <i class="fa fa-trash"></i>
            </button>
        </form>

        <!-- Add Songs Button -->
        <a href="playlists.php?playlist_id=<?php echo htmlspecialchars($pl['playlist_id']); ?>" class="update-btn">
            <i class="fa fa-plus"></i>
        </a>
    </td>
</tr>
<?php endforeach; ?>
