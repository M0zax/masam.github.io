<?php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "orderhistory";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user']) || !isset($data['cart'])) {
    echo json_encode(["message" => "Invalid data received"]);
    $conn->close();
    exit;
}

$name = $data['user']['name'];
$address = $data['user']['address'];
$email = $data['user']['email'];
$cart = $data['cart'];

$all_successful = true;

foreach ($cart as $item) {
    $services = $item['name'];
    $price = $item['price'];
    $quantity = $item['quantity'];
    $total = $price * $quantity;

    $sql = "INSERT INTO orders (services, price, quantity, total, name, address, email) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["message" => "Prepare statement failed: " . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param("sdiisss", $services, $price, $quantity, $total, $name, $address, $email);

    if (!$stmt->execute()) {
        $all_successful = false;
        error_log("Error submitting order: " . $stmt->error);
    }

    $stmt->close();
}

if ($all_successful) {
    echo json_encode(["message" => "Order submitted successfully!"]);
} else {
    echo json_encode(["message" => "There was an error submitting some items in your order. Please check the logs for more details."]);
}

$conn->close();
?>
