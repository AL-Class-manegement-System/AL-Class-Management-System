<?php
include('../../includes/connection.php');

$table_sql = "CREATE TABLE IF NOT EXISTS study_materials (
    material_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    class_id INT(11) NOT NULL,
    teacher_id INT(11) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_by INT(11) NOT NULL,
    uploaded_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT(1) DEFAULT 1
)";

// Note: uploaded_by essentially duplicates teacher_id if the uploader is the teacher, but following pattern from previous upload_materials.php code seen in history which used 'uploaded_by'. 
// I'll add both or check what the code expects. 
// In step 48: INSERT INTO study_materials (title, class_id, file_path, uploaded_by, uploaded_on, status)
// It didn't use teacher_id explicitly, it used uploaded_by provided with $teacher_db_id.
// So I will align the table with that.

$table_sql_corrected = "CREATE TABLE IF NOT EXISTS study_materials (
    material_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    class_id INT(11) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_by INT(11) NOT NULL, 
    uploaded_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT(1) DEFAULT 1
)";

if ($conn->query($table_sql_corrected) === TRUE) {
    echo "Table 'study_materials' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
