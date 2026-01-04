<?php
include('../../includes/connection.php');

$check_sql = "DESCRIBE classes";
$result = $conn->query($check_sql);

if ($result) {
    echo "Table 'classes' exists.\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
