<?php 
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
                <a href="../log/registration.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/30 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add New Student
                </a>
            </div>
        </header>

        <main class="p-8">
            
            <?php if(isset($_GET['msg'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center animate-pulse">
                <div>
                    <p class="font-bold">Success</p>
                    <p class="text-sm"><?php echo htmlspecialchars($_GET['msg']); ?></p>
                </div>
                <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900"><i class="fas fa-times"></i></button>
            </div>
            <?php endif; ?>

            <form method="GET" action="" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class="relative w-full md:w-96">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" 
                           placeholder="Search by Name or Reg No...">
                </div>
                
                <div class="flex gap-2">
                     <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-900 transition">Search</button>
                     <?php if(isset($_GET['search'])): ?>
                        <a href="student.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">Reset</a>
                     <?php endif; ?>
                </div>
            </form>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Student Info</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reg No</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stream</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            
                            <?php
                            $search_query = "";
                            if(isset($_GET['search']) && !empty($_GET['search'])){
                                $search = $conn->real_escape_string($_GET['search']);
                                $search_query = "WHERE full_name LIKE '%$search%' OR reg_number LIKE '%$search%'";
                            }

                            $sql = "SELECT * FROM students $search_query ORDER BY registered_date DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    
                                    $is_active = ($row['status'] == 1);
                                    $status_color = $is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                    $status_text = $is_active ? 'Active' : 'Inactive';
                                    
                                    // Status Button Logic
                                    $btn_class = $is_active ? 'text-red-500 bg-red-50 hover:bg-red-100' : 'text-green-600 bg-green-50 hover:bg-green-100';
                                    $btn_icon = $is_active ? 'fa-user-slash' : 'fa-user-check';
                                    $btn_title = $is_active ? 'Deactivate User' : 'Activate User';
                                    
                                    // Correct Image Path
                                    $photo_path = !empty($row['photo']) ? "../assests/images/students/" . $row['photo'] : "../assests/images/user2.jpg";
                            ?>

                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover border border-gray-200 shadow-sm" src="<?php echo $photo_path; ?>" alt="Photo">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['student_phone']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 font-mono font-bold bg-gray-100 px-2 py-1 rounded"><?php echo $row['reg_number']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium"><?php echo $row['stream']; ?></div>
                                    <div class="text-xs text-gray-500">Batch <?php echo $row['batch']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_color; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                                    
                                    <a href="#" class="p-2 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition" title="Edit Details">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="student_status.php?id=<?php echo $row['student_id']; ?>&status=<?php echo $row['status']; ?>" 
                                       class="p-2 rounded-lg transition <?php echo $btn_class; ?>" 
                                       title="<?php echo $btn_title; ?>"
                                       onclick="return confirm('Are you sure?')">
                                        <i class="fas <?php echo $btn_icon; ?>"></i>
                                    </a>

                                </td>
                            </tr>

                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-8 text-gray-500'>No students found.</td></tr>";
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