<?php
// ============================================
// ADMIN EXPORT TO PDF FILE
// This makes a PDF file containing all the grave records.
// It uses the SimplePdf helper (a small PDF builder).
// ============================================

// Load the database connection and the PDF helper.
require_once 'db.php';
require_once 'pdf_helper.php';

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

// Put all the records into a list.
$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}
$conn->close();

// Build the PDF (landscape = wide page, better for a table).
$pdf = new SimplePdf('L', 'pt', 'A4');
$pdf->setTitle('Aglipay Cemetery Records');

// Add the title and some info at the top.
$pdf->addTitle('AGLIPAY CEMETERY - GRAVE RECORDS');
$pdf->addText('Generated: ' . date('F j, Y g:i A'));
$pdf->addText('Total Records: ' . count($records));

$pdf->ln(6);

// Set up the table columns.
$pdf->setTableColumns([
    'UID',
    'Fullname',
    'Birthdate',
    'Deathdate',
    'Phase',
    'Column',
    'Row',
    'X',
    'Y',
    'Z'
]);

// Add one table row for every record.
foreach ($records as $r) {
    $pdf->addRow([
        $r['UID'],
        $r['Fullname'],
        $r['Birthdate'],
        $r['Deathdate'],
        $r['Phase'],
        $r['Column'],
        $r['Row'],
        $r['X'],
        $r['Y'],
        $r['Z']
    ]);
}

// Give the file a name with today's date and download it.
$filename = 'cemetery_records_' . date('Y-m-d') . '.pdf';
$pdf->output($filename);
?>
