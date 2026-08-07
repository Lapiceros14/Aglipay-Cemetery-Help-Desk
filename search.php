<?php
// ============================================
// SEARCH FILE
// This file searches the database for people whose name
// matches what the user typed in the search box.
// The result is sent back as JSON so the page can show it.
// ============================================

// Tell the browser that the answer we send back is JSON.
header("Content-Type: application/json");

// Load the database connection.
include "db.php";

// If there is no "name" in the web address OR it is empty,
// then send back an empty list and stop.
if (!isset($_GET['name']) || empty($_GET['name'])) {
    echo json_encode([]);   // [] means an empty list
    exit;                   // stop running the rest of this file
}

// Get the name the user typed.
// The % signs make the search match any name that CONTAINS the typed text.
// Example: "an" will match "sean", "Richsan", etc.
$name = "%" . trim($_GET['name']) . "%";

// Prepare the search query.
// The ? is a "placeholder" that gets filled safely later.
$stmt = $conn->prepare("SELECT * FROM cemetery_tb WHERE Fullname LIKE ?");

// Fill the placeholder with the actual name (s = string/text).
$stmt->bind_param("s", $name);

// Run the query on the database.
$stmt->execute();

// Get the results of the query.
$result = $stmt->get_result();

// Create an empty list to hold all the matching people.
$people = [];

// Loop through each row found and add it to the list.
while ($row = $result->fetch_assoc()) {
    $people[] = $row;
}

// Send the list of people back to the page as JSON.
echo json_encode($people);

// Close the statement and the connection (clean up).
$stmt->close();
$conn->close();
?>
