<?php

$uri = "/mock-response-api.php";

$mockStatus = $_SERVER["HTTP_X_MOCK_STATUS"] ?? "success";

$mockResponses = [
    "success" => ["message" => "Mock response for a successful request"],
    "error"   => ["error" => "Mock response for a failed request"],
];

if ($_SERVER["REQUEST_URI"] === $uri) {
    if (array_key_exists($mockStatus, $mockResponses)) {
        http_response_code($mockStatus === "success" ? 200 : 500);
        header("Content-Type: application/json");
        echo json_encode($mockResponses[$mockStatus]);
    } else {
        http_response_code(400);
        echo "Invalid X-Mock-Status header value";
    }
} else {
    http_response_code(404);
    echo "Endpoint not found";
}

?>