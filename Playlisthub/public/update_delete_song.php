<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] == 'update') {
        $song_id = $_POST['song_id'];
        $title = $_POST['title'];
        $artist = $_POST['artist'];
        $genre = $_POST['genre'];

        try {
            $stmt = $pdo->prepare("UPDATE songs SET title = ?, artist = ?, genre = ? WHERE song_id = ?");
            $stmt->execute([$title, $artist, $genre, $song_id]);
            echo "success";
        } catch (PDOException $e) {
            echo "Error updating song: " . $e->getMessage();
        }
    } elseif ($_POST['action'] == 'delete') {
        $song_id = $_POST['song_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM songs WHERE song_id = ?");
            $stmt->execute([$song_id]);
            echo "success";
        } catch (PDOException $e) {
            echo "Error deleting song: " . $e->getMessage();
        }
    }
}
?>
