🎵 PlaylistHub - PHP Playlist Management System

A web-based Playlist Management System built with PHP and MySQL. This project allows users to create, manage, and organize their playlists efficiently. It features an admin dashboard for overseeing user activity and playlist content.

🚀 Features

User Authentication: Register and log in securely.

Playlist Management: Add, edit, and delete playlists.

Song Management: Add, view, update, and delete songs within playlists.

Admin Dashboard: Monitor user activities, playlists, and songs with search functionality.

Responsive UI: Designed for desktop use with clean layouts.

📂 Project Structure

├── assets/               # Contains CSS, JS, and image files
├── public/               # Public-facing PHP files (index.php, playlists.php, etc.)
├── config/               # Database configuration and connection settings
├── playlisthub(1).sql     # SQL file to set up the database schema
└── README.md             # Project documentation

🗄️ Database Setup

Import the Database:

Open phpMyAdmin via XAMPP.

Create a new database named PlaylistHub.

Import playlisthub(1).sql into the newly created database.

Database Structure:The database includes the following tables:

users (user_id, username, email, password)

playlists (playlist_id, user_id, title, description)

songs (song_id, title, artist, album, genre, duration)

playlist_songs (playlist_song_id, playlist_id, song_id)

⚙️ Installation & Setup

Clone the Repository:

git clone https://github.com/your-username/PlaylistHub.git
cd PlaylistHub

Set Up XAMPP:

Place the project folder in htdocs (e.g., C:/xampp/htdocs/PlaylistHub).

Start Apache and MySQL in the XAMPP Control Panel.

Configure Database Connection:

Open config/db.php.

Update database credentials if needed:

$host = 'localhost';
$db = 'PlaylistHub';
$user = 'root';
$pass = '';

Run the Project:

Visit http://localhost/PlaylistHub/public/index.php in your browser.

🔑 Admin Credentials (if applicable)

Username: admin

Password: (default password or specify if set)

🎨 UI Features

Hover Effects: Buttons and links respond interactively.

Active States: Underlines active navigation links.

Smooth Transitions: Fade-in effects for enhanced UX.

🤝 Contributing

Contributions are welcome! Feel free to fork the repository, make improvements, and submit a pull request.
