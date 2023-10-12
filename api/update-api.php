<?php

function connectToDatabase() {
    $host = "MYSQL_HOST";
    $username = "MYSQL_USER";
    $password = "MYSQL_PASSWORD";
    $database = "MYSQL_DATABASE";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// API endpoint for updating a transaction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['transaction_id']) && isset($data['status'])) {
        $transaction_id = $data['transaction_id'];
        $status = $data['status'];

        $conn = connectToDatabase();

        $sql = "UPDATE transactions SET status=$status WHERE id=$transaction_id ";

        if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
        } else {
        echo "Error updating record: " . $conn->error;
        }

        $conn->close();
    } else {
        echo json_encode(["error" => "Invalid input data"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}