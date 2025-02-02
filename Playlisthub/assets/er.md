# ER Diagram for Playlist Management System

This is an ER (Entity-Relationship) diagram for our playlist management system, created using Mermaid.

```mermaid
erDiagram
    User {
        int UserID PK
        string Username
        string Password
        string Email
    }

    Songs {
        int SongID PK
        string SongName
        string Artist
        string Genre
        int Duration
    }

    Playlists {
        int PlaylistID PK
        string PlaylistName
        int UserID FK
    }

    Playlist_Songs {
        int PlaylistID FK
        int SongID FK
    }

    User "1" -- "*" Playlist : "creates"
    Playlist "1" -- "*" Playlist_Songs : "contains"
    Song "1" -- "*" Playlist_Songs : "is in"
