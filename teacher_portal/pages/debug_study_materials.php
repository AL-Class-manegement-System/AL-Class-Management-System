<?php
include('../../includes/connection.php');

$check_sql = "DESCRIBE study_materials";
$result = $conn->query($check_sql);

if ($result) {
    echo "Table 'study_materials' exists.\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table 'study_materials' does not exist.\n";
    echo "Error: " . $conn->error;
}
?>
