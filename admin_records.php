<?php
// ============================================
// ADMIN RECORDS FILE
// This file gets ALL grave records from the database
// and sends them back as JSON.
// The admin page uses this to show the table of records.
// ============================================

// Load the database connection.
require_once 'db.php';

// Tell the browser that the answer is JSON.
header('Content-Type: application/json');

// Get all records from the table, ordered by UID (from smallest to largest).
$result = $conn->query(
    "SELECT `UID`, `Fullname`, `Birthdate`, `Deathdate`, `Phase`, `Column`, `Row`, `X`, `Y`, `Z`
     FROM `cemetery_tb`
     ORDER BY `UID` ASC"
);

// Create an empty list to hold all the records.
$records = [];

// If the query worked, loop through each row and add it to the list.
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
}

// Close the connection (we are done using the database).
$conn->close();

// Send the list of records back as JSON.
echo json_encode(['success' => true, 'records' => $records]);
?>
