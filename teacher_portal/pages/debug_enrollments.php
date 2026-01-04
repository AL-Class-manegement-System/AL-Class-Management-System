<?php
include('../../includes/connection.php');

$check_sql = "DESCRIBE enrollments";
$result = $conn->query($check_sql);

if ($result) {
    echo "Table 'enrollments' exists.\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table 'enrollments' does not exist or error: " . $conn->error;
}
?>
