<?php
// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "user_db";

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $birthday = $_POST['birthday'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into `users` table
    $stmt = $conn->prepare("INSERT INTO users (name, age, birthday, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $name, $age, $birthday, $address);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id; // Get the last inserted ID

        // Insert into `login` table
        $login_stmt = $conn->prepare("INSERT INTO login (username, password, user_id) VALUES (?, ?, ?)");
        $login_stmt->bind_param("ssi", $username, $hashed_password, $user_id);

        if ($login_stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $login_stmt->error;
        }

        $login_stmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
