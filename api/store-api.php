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


$firstApiUrl = "http://localhost:8080/mock-response-api.php";

function generateTransactionId($user_id, $amount) {
    return $user_id . '-' . $amount . '-' . time();
}

function storePaymentInformation($user_id, $amount, $firstApiResponse, $transactionId) {

    $conn = connectToDatabase();

    $sql = "INSERT INTO transactions (transactionId, user_id, amount)
    VALUES ($user_id, $amount,$transactionId)";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['user_id']) && isset($data['amount'])) {
        $user_id = $data['user_id'];
        $amount = $data['amount'];
        
        // Call the first API
        $firstApiResponse = file_get_contents($firstApiUrl, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => json_encode(['user_id' => $user_id, 'amount' => $amount]),
            ],
        ]));

        if ($firstApiResponse !== false) {
            $firstApiResponse = json_decode($firstApiResponse, true);

            $transactionId = generateTransactionId($user_id, $amount);

            // Store necessary information
            storePaymentInformation($user_id, $amount, $firstApiResponse, $transactionId);

            // Respond with Cache-Control: no-store header
            header("Cache-Control: no-store");
            
            // Respond with the transaction ID
            echo json_encode(['transaction_id' => $transactionId]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to process payment']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
