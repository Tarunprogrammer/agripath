<?php
header('Content-Type: application/json');
require_once 'includes/llama_api.php';

// Get the JSON input from the dashboard
$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';

if (empty($prompt)) {
    echo json_encode(['error' => 'No prompt provided']);
    exit;
}

// Call your existing askLlama function
$response = askLlama($prompt);

// Return the response as JSON
echo json_encode(['response' => $response]);
?>