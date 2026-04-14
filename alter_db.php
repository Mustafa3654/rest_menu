<?php
include "connection.php";

$sql = "ALTER TABLE settings ADD COLUMN theme_color VARCHAR(20) DEFAULT '#1a2a6c'";

if ($conn->query($sql) === TRUE) {
    echo "Column theme_color added successfully.";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
