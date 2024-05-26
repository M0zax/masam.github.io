<?php
// Display all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database credentials
$servername = "localhost";
$db_username = "root"; // Default XAMPP username
$db_password = ""; // Default XAMPP password
$dbname = "orderhistory";

// Create database connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check database connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error); // Log error
    http_response_code(500);
    echo "Internal Server Error";
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if username or password is empty
    if (empty($username) || empty($password)) {
        echo "Username and password cannot be empty.";
        exit();
    }

    // Check if the username already exists
    $sql_check = "SELECT COUNT(*) FROM userinfo WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check) {
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            // Username already exists, display error message
            echo "Username already taken. Please choose a different username.";
            exit();
        }
    } else {
        error_log("SQL error: " . $conn->error); // Log error
        http_response_code(500);
        echo "Internal Server Error";
        exit();
    }

    // Prepare SQL query to insert user data into the database
    $sql_insert = "INSERT INTO userinfo (username, password) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    if ($stmt_insert) {
        // Bind parameters and execute query
        $stmt_insert->bind_param("ss", $username, $password);
        if ($stmt_insert->execute()) {
            // Registration successful
            echo "Registration successful!";
            // Optionally, you can redirect the user to another page after successful registration
            // header("Location: file1.html");
            exit();
        } else {
            // SQL execution error
            error_log("Execution error: " . $stmt_insert->error); // Log error
            http_response_code(500);
            echo "Internal Server Error";
        }
        $stmt_insert->close();
    } else {
        // SQL preparation error
        error_log("Preparation error: " . $conn->error); // Log error
        http_response_code(500);
        echo "Internal Server Error";
    }
}

// Close database connection
$conn->close();
?>
