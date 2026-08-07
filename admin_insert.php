<?php
// ============================================
// ADMIN INSERT FILE
// This file adds a NEW grave record to the database.
// The admin page sends the record details as JSON,
// and this file saves them into the database.
// ============================================

// Load the database connection.
require_once 'db.php';

// Tell the browser that the answer is JSON.
header('Content-Type: application/json');

// We start with a "not ok" answer. We change it to "ok" if everything works.
$response = ['success' => false, 'message' => ''];

// This file can only be used with a POST request (not GET).
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

// --- Get all the values from the data ---
// The ??  means "if it is not set, use this empty value instead".
$fullname  = trim($data['Fullname'] ?? '');
$birthdate = trim($data['Birthdate'] ?? '');
$deathdate = trim($data['Deathdate'] ?? '');
$phase     = isset($data['Phase']) ? $data['Phase'] : '';
$column    = isset($data['Column']) ? $data['Column'] : '';
$row       = isset($data['Row']) ? $data['Row'] : '';
$x         = isset($data['X']) ? $data['X'] : '';
$y         = isset($data['Y']) ? $data['Y'] : '';
$z         = isset($data['Z']) ? $data['Z'] : '';

// --- Check that the important fields are not empty ---

if ($fullname === '') {
    $response['message'] = 'Fullname is required.';
    echo json_encode($response);
    exit;
}

if ($birthdate === '' || $deathdate === '') {
    $response['message'] = 'Birthdate and Deathdate are required.';
    echo json_encode($response);
    exit;
}

if ($phase === '' || $column === '' || $row === '') {
    $response['message'] = 'Phase, Column, and Row are required.';
    echo json_encode($response);
    exit;
}

if ($x === '' || $y === '' || $z === '') {
    $response['message'] = 'X, Y, and Z coordinates are required.';
    echo json_encode($response);
    exit;
}

// --- Check that the numbers are actually numbers ---

if (!is_numeric($phase) || !is_numeric($column) || !is_numeric($row) ||
    !is_numeric($x) || !is_numeric($y) || !is_numeric($z)) {
    $response['message'] = 'Phase, Column, Row, X, Y, and Z must be numeric.';
    echo json_encode($response);
    exit;
}

// --- Insert the record using a prepared statement (safe) ---

$stmt = $conn->prepare(
    "INSERT INTO `cemetery_tb`
        (`Fullname`, `Birthdate`, `Deathdate`, `Phase`, `Column`, `Row`, `X`, `Y`, `Z`)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

// If the statement failed to prepare, stop.
if (!$stmt) {
    $response['message'] = 'Database error: ' . $conn->error;
    echo json_encode($response);
    exit;
}

// Change the values into the right types for the database.
$phaseInt  = (int)$phase;
$columnInt = (int)$column;
$rowInt    = (int)$row;
$xFloat    = (float)$x;
$yFloat    = (float)$y;
$zFloat    = (float)$z;

// Fill the placeholders with the actual values.
// s = string/text, i = integer/number, d = decimal/float
$stmt->bind_param(
    'sssiiiddd',
    $fullname,
    $birthdate,
    $deathdate,
    $phaseInt,
    $columnInt,
    $rowInt,
    $xFloat,
    $yFloat,
    $zFloat
);

// Try to add the record.
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Record added successfully.';
    // insert_id is the new record's UID number.
    $response['uid'] = $stmt->insert_id;
} else {
    $response['message'] = 'Insert failed: ' . $stmt->error;
}

// Clean up: close the statement and the connection.
$stmt->close();
$conn->close();

// Send the final answer back to the page.
echo json_encode($response);
?>
