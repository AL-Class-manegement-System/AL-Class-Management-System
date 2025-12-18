<?php 
// admin/student.php
include 'db_con.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40">
            <h2 class="text-2xl font-bold text-gray-800">Student Management</h2>
            <div class="flex items-center gap-4">
                <a href="../log/registration.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add New Student
                </a>
            </div>
        </header>

        <main class="p-8">
            
            <?php if(isset($_GET['msg'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center">
                <div>
                    <p class="font-bold">Success</p>
                    <p class="text-sm"><?php echo htmlspecialchars($_GET['msg']); ?></p>
                </div>
                <button onclick="this.parentElement.style.display='none'" class="text-green-700"><i class="fas fa-times"></i></button>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center">
                <div>
                    <p class="font-bold">Error</p>
                    <p class="text-sm"><?php echo htmlspecialchars($_GET['error']); ?></p>
                </div>
                <button onclick="this.parentElement.style.display='none'" class="text-red-700"><i class="fas fa-times"></i></button>
            </div>
            <?php endif; ?>

            <form method="GET" action="" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    
                    <div class="w-full md:flex-1">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Search Student</label>
                        <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" 
                            placeholder="Name or Reg No...">
                    </div>

                    <div class="w-full md:w-48">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Stream</label>
                        <select name="stream" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                            <option value="">All Streams</option>
                            <option value="Maths" <?php if(isset($_GET['stream']) && $_GET['stream'] == 'Maths') echo 'selected'; ?>>Physical Science</option>
                            <option value="Bio" <?php if(isset($_GET['stream']) && $_GET['stream'] == 'Bio') echo 'selected'; ?>>Bio Science</option>
                            <option value="Tech" <?php if(isset($_GET['stream']) && $_GET['stream'] == 'Tech') echo 'selected'; ?>>Technology</option>
                            <option value="Commerce" <?php if(isset($_GET['stream']) && $_GET['stream'] == 'Commerce') echo 'selected'; ?>>Commerce</option>
                            <option value="Art" <?php if(isset($_GET['stream']) && $_GET['stream'] == 'Art') echo 'selected'; ?>>Arts</option>
                        </select>
                    </div>

                    <div class="w-full md:w-32">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Batch</label>
                        <select name="batch" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                            <option value="">All Years</option>
                            <option value="2024" <?php if(isset($_GET['batch']) && $_GET['batch'] == '2024') echo 'selected'; ?>>2024</option>
                            <option value="2025" <?php if(isset($_GET['batch']) && $_GET['batch'] == '2025') echo 'selected'; ?>>2025</option>
                            <option value="2026" <?php if(isset($_GET['batch']) && $_GET['batch'] == '2026') echo 'selected'; ?>>2026</option>
                            <option value="2027" <?php if(isset($_GET['batch']) && $_GET['batch'] == '2027') echo 'selected'; ?>>2027</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/30">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>

                        <button type="submit" formaction="export_students.php" name="action" value="csv" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition shadow-lg" title="Download CSV">
                            <i class="fas fa-file-csv mr-1"></i> CSV
                        </button>

                        <button type="submit" formaction="export_students.php" name="action" value="pdf" formtarget="_blank" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-600 transition shadow-lg" title="Print PDF">
                            <i class="fas fa-file-pdf mr-1"></i> PDF
                        </button>
                        
                        <?php if(isset($_GET['search']) || isset($_GET['stream']) || isset($_GET['batch'])): ?>
                            <a href="student.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                                Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Student Info</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Reg No</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Stream</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            
                            <?php
                            $sql = "SELECT * FROM students WHERE 1=1";
                            $params = [];
                            $types = '';

                            if(isset($_GET['search']) && !empty($_GET['search'])){
                                $search = "%" . $_GET['search'] . "%";
                                $sql .= " AND (full_name LIKE ? OR reg_number LIKE ?)";
                                $types .= "ss";
                                $params[] = $search;
                                $params[] = $search;
                            }

                            if(isset($_GET['stream']) && !empty($_GET['stream'])){
                                $stream = $_GET['stream'];
                                $sql .= " AND stream = ?";
                                $types .= "s";
                                $params[] = $stream;
                            }

                            if(isset($_GET['batch']) && !empty($_GET['batch'])){
                                $batch = $_GET['batch'];
                                $sql .= " AND batch = ?";
                                $types .= "s";
                                $params[] = $batch;
                            }

                            $sql .= " ORDER BY student_id DESC";
                            
                            $result = false;
                            $stmt = $conn->prepare($sql);

                            if ($stmt) {
                                if (!empty($params)) {
                                    $stmt->bind_param($types, ...$params); 
                                }
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $stmt->close();
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-8 text-red-500'>Database Query Error.</td></tr>";
                            }


                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    
                                    $status_val = isset($row['status']) ? $row['status'] : 1; 
                                    $is_active = ($status_val == 1);
                                    
                                    $status_color = $is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                    $status_text = $is_active ? 'Active' : 'Inactive';
                                    
                                    $photo_name = $row['photo'];
                                    $photo_path = (!empty($photo_name) && file_exists("../assets/images/students/" . $photo_name)) 
                                        ? "../assets/images/students/" . $photo_name 
                                        : "../assets/images/user2.jpg"; 
                            ?>

                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover border" src="<?php echo $photo_path; ?>" alt="Photo">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['student_phone']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold bg-gray-100 px-2 py-1 rounded"><?php echo $row['reg_number']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium"><?php echo $row['stream']; ?></div>
                                    <div class="text-xs text-gray-500">Batch <?php echo $row['batch']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full <?php echo $status_color; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                                    
                                    <a href="student_enrollments.php?student_id=<?php echo $row['student_id']; ?>" 
                                       class="p-2 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition border border-blue-200" 
                                       title="View Enrollment History">
                                        <i class="fas fa-history"></i>
                                    </a>

                                    <a href="print_id_card.php?id=<?php echo $row['student_id']; ?>" target="_blank" class="p-2 rounded-lg text-purple-600 bg-purple-50 hover:bg-purple-100 transition" title="Print ID Card">
                                        <i class="fas fa-id-card"></i>
                                    </a>

                                    <a href="#" class="p-2 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="student_status.php?id=<?php echo $row['student_id']; ?>&status=<?php echo $status_val; ?>" 
                                       class="p-2 rounded-lg <?php echo $is_active ? 'text-red-500 bg-red-50' : 'text-green-600 bg-green-50'; ?>" 
                                       onclick="return confirm('Change status?')"
                                       title="Change Status">
                                        <i class="fas <?php echo $is_active ? 'fa-user-slash' : 'fa-user-check'; ?>"></i>
                                    </a>

                                </td>
                            </tr>

                            <?php 
                                }
                            } else if ($result !== false) {
                                echo "<tr><td colspan='5' class='text-center py-8 text-gray-500'>No students found matching your filters.</td></tr>";
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</body>
</html>