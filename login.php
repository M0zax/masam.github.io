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
    http_response_code(500);
    echo "Connection failed: " . $conn->connect_error;
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL query to check user credentials
    $sql = "SELECT password FROM userinfo WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters and execute query
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            // Bind result variables and fetch the result
            $stmt->bind_result($db_password);
            if ($stmt->fetch()) {
                // Verify the password
                if ($password == $db_password) {
                    // Set session variables
                    $_SESSION['username'] = $username;
                    // Output JavaScript alert message
                    echo "<script>alert('Login successful!');</script>";
                    // Redirect to home page
                    header("Location: file1.html");
                    exit();
                } else {
                    // Incorrect password
                    http_response_code(401);
                    echo "Invalid username or password.";
                }
            } else {
                // Username not found
                http_response_code(401);
                echo "Invalid username or password.";
            }
            $stmt->close();
        } else {
            // SQL execution error
            http_response_code(500);
            echo "Error: " . $stmt->error;
        }
    } else {
        // SQL preparation error
        http_response_code(500);
        echo "Error: " . $conn->error;
    }
}

// Close database connection
$conn->close();
?>
