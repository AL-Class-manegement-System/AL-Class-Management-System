<?php
// admin/export_students.php
session_start();
include('includes/auth.php');
include('db_con.php');

// ==========================================
// 1. FILTER LOGIC (Same as student.php)
// ==========================================
$where = "WHERE 1=1";
$params = [];
$types = "";

// Search Logic
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $where .= " AND (full_name LIKE ? OR reg_number LIKE ?)";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

// Stream Logic
if (isset($_GET['stream']) && !empty($_GET['stream'])) {
    $where .= " AND stream = ?";
    $params[] = $_GET['stream'];
    $types .= "s";
}

// Batch Logic
if (isset($_GET['batch']) && !empty($_GET['batch'])) {
    $where .= " AND batch = ?";
    $params[] = $_GET['batch'];
    $types .= "s";
}

// Fetch Data
$sql = "SELECT * FROM students $where ORDER BY reg_number ASC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// ==========================================
// 2. EXPORT HANDLER
// ==========================================
$action = isset($_GET['action']) ? $_GET['action'] : 'csv';

if ($action == 'csv') {
    // --- CSV EXPORT ---
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_report_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');

    // Column Headers
    fputcsv($output, array('Reg Number', 'Full Name', 'Parent Phone', 'Student Phone', 'Address', 'Stream', 'Batch', 'School', 'Status'));

    // Rows
    while ($row = $result->fetch_assoc()) {
        $status = ($row['status'] == 1) ? 'Active' : 'Inactive';
        fputcsv($output, array(
            $row['reg_number'],
            $row['full_name'],
            $row['parent_phone'],
            $row['student_phone'],
            $row['address'],
            $row['stream'],
            $row['batch'],
            $row['school'],
            $status
        ));
    }
    fclose($output);
    exit();

} elseif ($action == 'pdf') {
    // --- PDF / PRINT VIEW ---
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Student Report - PDF</title>
        <style>
            body { font-family: sans-serif; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .header { text-align: center; margin-bottom: 20px; }
            .header h1 { margin: 0; font-size: 18px; }
            .header p { margin: 5px 0; }
            @media print {
                .no-print { display: none; }
            }
        </style>
    </head>
    <body onload="window.print()">
        
        <div class="no-print" style="margin-bottom: 20px;">
            <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Report</button>
            <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Close</button>
        </div>

        <div class="header">
            <h1>Student Report</h1>
            <p>Generated on: <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>Name</th>
                    <th>Stream</th>
                    <th>Batch</th>
                    <th>Parent Phone</th>
                    <th>School</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        $status = ($row['status'] == 1) ? 'Active' : 'Inactive';
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['reg_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['stream']); ?></td>
                            <td><?php echo htmlspecialchars($row['batch']); ?></td>
                            <td><?php echo htmlspecialchars($row['parent_phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['school']); ?></td>
                            <td><?php echo $status; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit();
}
?>