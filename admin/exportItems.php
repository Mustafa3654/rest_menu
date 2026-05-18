<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="items_export_' . date('Y-m-d_H-i-s') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for Excel UTF-8 compatibility
fputs($output, "\xEF\xBB\xBF");

// Write CSV headers
fputcsv($output, ['ID', 'Name', 'Category', 'Price USD', 'Price Suffix', 'Ingredients', 'Image Path']);

// Fetch all items from database
$sql = "SELECT item_id, item_name, item_category, item_priceusd, price_suffix, Ingredients, item_pic FROM items ORDER BY item_category, item_name";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['item_id'],
            $row['item_name'],
            $row['item_category'],
            $row['item_priceusd'],
            $row['price_suffix'],
            $row['Ingredients'],
            $row['item_pic']
        ]);
    }
}

fclose($output);
exit;
?>



