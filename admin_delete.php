<?php
// ============================================
// ADMIN DELETE FILE
// This file deletes a grave record from the database.
// The admin page sends the record's UID (unique ID number),
// and this file removes that record.
// ============================================

// Load the database connection.
require_once 'db.php';

// Tell the browser that the answer is JSON.
header('Content-Type: application/json');

// We start with a "not ok" answer. We change it to "ok" if everything works.
$response = ['success' => false, 'message' => ''];

// This file can only be used with a POST request (not GET).
// POST is usually used when sending data to the server.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

// The admin page sends the data as JSON in the request body.
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

// If the data is not in the right format, stop.
if (!is_array($data)) {
    $response['message'] = 'Invalid JSON payload.';
    echo json_encode($response);
    exit;
}

// --- Get the UID from the data ---
$uid = isset($data['UID']) ? $data['UID'] : '';

// A UID must not be empty and must be a number.
if ($uid === '' || !is_numeric($uid)) {
    $response['message'] = 'A valid record UID is required.';
    echo json_encode($response);
    exit;
}

// Turn the UID into a whole number.
$uidInt = (int)$uid;

// --- Delete the record using a prepared statement (safe) ---
$stmt = $conn->prepare("DELETE FROM `cemetery_tb` WHERE `UID` = ?");

// If the statement failed to prepare, stop.
if (!$stmt) {
    $response['message'] = 'Database error: ' . $conn->error;
    echo json_encode($response);
    exit;
}

// Fill the placeholder with the UID (i = integer/number).
$stmt->bind_param('i', $uidInt);

// Try to delete the record.
if ($stmt->execute()) {
    // affected_rows tells us how many records were deleted.
    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Record (UID: ' . $uidInt . ') deleted successfully.';
    } else {
        // No record was deleted, meaning the UID did not exist.
        $response['message'] = 'No record found with UID: ' . $uidInt . '.';
    }
} else {
    $response['message'] = 'Delete failed: ' . $stmt->error;
}

// Clean up: close the statement and the connection.
$stmt->close();
$conn->close();

// Send the final answer back to the page.
echo json_encode($response);
?>
