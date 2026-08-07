<?php
// ============================================
// ADMIN EXPORT TO EXCEL FILE
// This makes a CSV file (which opens in Excel)
// containing all the grave records.
// ============================================

// Load the database connection.
require_once 'db.php';

// Get all records from the table, ordered by UID.
$result = $conn->query(
    "SELECT `UID`, `Fullname`, `Birthdate`, `Deathdate`, `Phase`, `Column`, `Row`, `X`, `Y`, `Z`
     FROM `cemetery_tb`
     ORDER BY `UID` ASC"
);

// If the query failed, stop and show the error.
if (!$result) {
    die('Query failed: ' . $conn->error);
}

// Give the file a name with today's date in it.
$filename = 'cemetery_records_' . date('Y-m-d') . '.csv';

// Tell the browser to download the file (not show it).
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Open a "virtual file" that sends data straight to the download.
$out = fopen('php://output', 'w');

// Add a special marker (BOM) so Excel reads our text correctly.
fwrite($out, "\xEF\xBB\xBF");

// Write the header row (the column names).
fputcsv($out, ['UID', 'Fullname', 'Birthdate', 'Deathdate', 'Phase', 'Column', 'Row', 'X', 'Y', 'Z']);

// Write one row for every record.
while ($row = $result->fetch_assoc()) {
    fputcsv($out, [
        $row['UID'],
        $row['Fullname'],
        $row['Birthdate'],
        $row['Deathdate'],
        $row['Phase'],
        $row['Column'],
        $row['Row'],
        $row['X'],
        $row['Y'],
        $row['Z']
    ]);
}

// Close the virtual file and the database connection.
fclose($out);
$conn->close();
exit;
?>
