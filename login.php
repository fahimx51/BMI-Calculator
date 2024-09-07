<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";  // Modify with your MySQL password
$dbname = "bmi";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to fetch password from the database
    $sql = "SELECT password FROM reg_user WHERE email = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        // Check if passwords match
        if ($password === $stored_password) {
            $_SESSION['username'] = $username;
            header("Location: bmi.php");
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username.";
    }
}

$conn->close();
?>
