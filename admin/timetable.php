<?php 
include 'db_con.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Time Tables - Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40">
            <h2 class="text-2xl font-bold text-gray-800">Time Table Management</h2>
            
            <a href="add_timetable.php" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-lg flex items-center gap-2">
                <i class="fas fa-plus"></i> Add New Class
            </a>
        </header>

        <main class="p-8">
            
            <?php if(isset($_GET['msg'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center">
                <span><?php echo htmlspecialchars($_GET['msg']); ?></span>
                <button onclick="this.parentElement.style.display='none'" class="text-green-700"><i class="fas fa-times"></i></button>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Class Name</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Subject & Stream</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Teacher</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fee</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            
                            <?php
                            $sql = "SELECT * FROM classes ORDER BY class_id DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                            ?>

                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($row['class_name']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-semibold"><?php echo htmlspecialchars($row['subject']); ?></div>
                                    <div class="text-xs text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded inline-block mt-1">
                                        <?php echo htmlspecialchars($row['stream']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">
                                        <i class="fas fa-user-tie text-gray-400 mr-2"></i><?php echo htmlspecialchars($row['teacher_name']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo $row['day']; ?>
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1 pl-1"><i class="far fa-clock mr-1"></i><?php echo $row['time']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-bold">
                                    Rs. <?php echo number_format($row['fee'], 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-3">
                                    <a href="edit_timetable.php?id=<?php echo $row['class_id']; ?>" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-2 rounded-lg transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_timetable.php?id=<?php echo $row['class_id']; ?>" 
                                       onclick="return confirm('Are you sure?');" 
                                       class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-lg transition" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>

                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-10 text-gray-500'>No classes found. Please add a new class.</td></tr>";
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