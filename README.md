# Miru: A Simple Video Streaming Platform

## Project Description

Miru is a robust yet simple video streaming platform designed to curate and display YouTube videos via iframes, offering users a seamless and intuitive experience to browse, search, and watch categorized content. A secure, database-driven admin panel allows for comprehensive management of video entries and categories, including a custom user login system. Built with PHP and MySQL, and styled using Bootstrap 5, Miru boasts an elegant, Netflix-inspired user interface, uniquely colored with the vibrant palette of Ijichi Nijika (featuring bright yellows, contrasting darks, and whites), and enhanced by modern typography with 'Open Sans ExtraBold' for headings and 'Montserrat' for body text.

## Features

**User-Facing:**
* **Video Browse:** Explore the latest videos on the homepage or discover content by category.
* **Integrated Playback:** Watch YouTube videos directly on the site via embedded iframes.
* **Search Functionality:** Easily find specific videos by title or description.
* **Responsive Design:** Optimized for seamless viewing across desktops, tablets, and mobile devices.
* **Elegant UI:** A visually appealing interface inspired by popular streaming platforms, featuring a unique color scheme and refined typography.

**Admin Panel:**
* **Secure Login:** A dedicated, database-driven authentication system protects the admin area.
* **Category Management (CRUD):** Full control to Create, Read, Update, and Delete video categories.
* **Video Management (CRUD):** Comprehensive tools to Create, Read, Update, and Delete video entries, including title, description, YouTube URL, and category assignment. Automatic extraction of YouTube thumbnails.

## Technologies Used

* **Backend:** PHP (>= 7.4 recommended)
* **Database:** MySQL (or MariaDB)
* **Frontend:**
    * HTML5
    * CSS3
    * Bootstrap 5 (CSS Framework)
    * Google Fonts ('Open Sans ExtraBold', 'Montserrat')
* **Web Server:** Apache (commonly used with XAMPP/WAMP/MAMP)

## Installation & Setup

Follow these steps to get the project up and running on your local machine.

### Prerequisites

* **Web Server Environment:** XAMPP, WAMP, MAMP, or a similar environment with Apache, PHP, and MySQL/MariaDB installed.
* **Code Editor:** Visual Studio Code, Sublime Text, etc.

### 1. Database Setup

1.  **Create Database:**
    * Open your web browser and go to `http://localhost/phpmyadmin` (or your preferred database management tool).
    * Create a new database named `video_streamer_db`.
2.  **Create Tables & Admin User:**
    * Select the `video_streamer_db` database.
    * Go to the `SQL` tab and execute the following SQL queries:

    ```sql
    -- categories table
    CREATE TABLE categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE
    );

    -- videos table
    CREATE TABLE videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        youtube_url VARCHAR(255) NOT NULL,
        thumbnail_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    );

    -- users table for admin login
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL, -- Store hashed passwords
        role VARCHAR(50) DEFAULT 'user', -- e.g., 'admin', 'moderator', 'user'
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ```
    * **Add an Admin User:**
        1.  Create a temporary PHP file (e.g., `hash_password.php`) in your project root:
            ```php
            <?php
            echo password_hash('your_secret_admin_password', PASSWORD_DEFAULT);
            ?>
            ```
            **Replace `'your_secret_admin_password'` with a strong password you'll use for admin.**
        2.  Run this file in your browser (e.g., `http://localhost/your-project-folder/hash_password.php`).
        3.  Copy the long hashed string outputted.
        4.  Insert the admin user into your `users` table via phpMyAdmin's SQL tab:
            ```sql
            INSERT INTO users (username, password, role) VALUES ('admin', 'PASTE_YOUR_HASHED_PASSWORD_HERE', 'admin');
            ```
            **Replace `'PASTE_YOUR_HASHED_PASSWORD_HERE'` with the actual hashed string you copied.**

### 2. Project Files

1.  **Clone/Download:** Clone this repository or download the project files.
2.  **Place in Web Server Root:**
    * Place the entire `Miru` folder (or whatever you name it) into your web server's document root (e.g., `C:\xampp\htdocs\`, `C:\wamp\www\`, `/Applications/MAMP/htdocs/`, or `/var/www/html/`).
    * Your project URL will be `http://localhost/Miru/` (assuming you name the folder `Miru`).

### 3. Configuration

1.  **`includes/config.php`:**
    * Open the file `Miru/includes/config.php`.
    * Update the database connection details if they differ from the defaults:
        ```php
        <?php
        DEFINE('DB_HOST', 'localhost');
        DEFINE('DB_USER', 'root'); // Your MySQL username (e.g., 'root')
        DEFINE('DB_PASS', '');     // Your MySQL password (e.g., '' for root, or your password)
        DEFINE('DB_NAME', 'video_streamer_db');

        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        ?>
        ```
2.  **`.htaccess` (Removed):**
    * Ensure there is **NO `.htaccess` file** in your project root. We are using traditional PHP URLs (`.php?id=`). If you previously added one, delete or rename it.
    * **Restart Apache** after any `.htaccess` changes or removal.

### 4. Placeholder Image

* Create a simple placeholder image (e.g., a gray rectangle) named `placeholder.jpg` in `assets/images/`. This will be used if YouTube thumbnails are not available.

## Usage

1.  **Access Miru Website:**
    * Open your web browser and go to `http://localhost/Miru/`
2.  **Access Miru Admin Panel:**
    * Go to `http://localhost/Miru/admin/login.php`
    * Log in using the `admin` username and the plain-text password you used when hashing.

## Styling & Design

Miru features a distinct visual style:

* **Netflix-Inspired Layout:** Clean, card-based content display, and an intuitive navigation bar.
* **Ijichi Nijika's Color Palette:** A unique theme utilizing bright yellow (`#FFD700`), lighter yellow (`#FFFACD`), and contrasting dark grays/blacks (`#1A1A1A`, `#2C2C2C`) with white text.
* **Modern Typography:** Headings and prominent text use **'Open Sans ExtraBold'** for a strong, clean look, while body text uses **'Montserrat'** for readability, both loaded from Google Fonts.
* **Smooth Transitions:** Subtle hover effects and animations enhance the user experience.

## Contributing

Feel free to fork this repository, open issues, or submit pull requests.

## License

This project is open-sourced under the MIT License. See the [LICENSE](LICENSE) file for more details.
