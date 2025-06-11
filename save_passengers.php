<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data === null) {
        throw new Exception('Invalid JSON data received');
    }

    // Validate the data
    foreach ($data as $passenger) {
        if (empty($passenger['firstName']) || empty($passenger['lastName']) || empty($passenger['phone'])) {
            throw new Exception('Missing required fields');
        }
        
        // Validate phone number (10 digits)
        if (!preg_match('/^[0-9]{10}$/', $passenger['phone'])) {
            throw new Exception('Invalid phone number format');
        }
    }

    // Format the data for storage
    $formattedData = '';
    foreach ($data as $index => $passenger) {
        $formattedData .= "Passenger " . ($index + 1) . ":\n";
        $formattedData .= "Name: " . $passenger['firstName'] . " " . $passenger['lastName'] . "\n";
        $formattedData .= "Gender: " . $passenger['gender'] . "\n";
        $formattedData .= "Phone: " . $passenger['phone'] . "\n";
        $formattedData .= "DOB: " . ($passenger['dob'] ?? 'Not provided') . "\n";
        $formattedData .= "----------------------------------------\n";
    }

    // Save to file with timestamp
    $filename = 'passengers_' . date('Y-m-d_H-i-s') . '.txt';
    if (file_put_contents($filename, $formattedData) === false) {
        throw new Exception('Failed to save passenger data');
    }

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Passenger data saved successfully',
        'filename' => $filename
    ]);

} catch (Exception $e) {
    // Send error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 