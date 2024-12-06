<?php
session_start(); // Start the session to manage login state

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "user_db"; // Your database name

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['message'] = "All fields are required.";
        $_SESSION['alert_type'] = "danger";
        header("Location: login.php");
        exit();
    }

    // Query to check user credentials in the `login` table
    $stmt = $conn->prepare("SELECT * FROM login WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $login = $result->fetch_assoc();

        // Validate the password (hashed comparison recommended for production)
        if (password_verify($password, $login['password'])) {
            // Set session variables for user authentication
            $_SESSION['user_id'] = $login['user_id'];
            $_SESSION['username'] = $username;

            // Redirect to index.php after successful login
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['message'] = "Invalid password.";
            $_SESSION['alert_type'] = "danger";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "No account found with that username.";
        $_SESSION['alert_type'] = "danger";
        header("Location: login.php");
        exit();
    }

}
?>
