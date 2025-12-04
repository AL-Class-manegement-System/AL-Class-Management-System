<?php 
include 'db_con.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <?php include('includes/sidebar.php'); ?>

    <div class="ml-64 flex flex-col min-h-screen">
        
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40">
            <h2 class="text-2xl font-bold text-gray-800">Teachers Management</h2>
            <div class="flex items-center gap-4">
                <a href="add_teacher.php" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add New Teacher
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
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Error</p>
                <p class="text-sm"><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                
                <?php
                $sql = "SELECT * FROM teachers WHERE status = 1 ORDER BY teacher_id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Image Path Handling
                        $img = $row['image'];
                        $imgPath = "../assets/images/teachers/" . $img;
                        
                        if (empty($img) || !file_exists($imgPath)) {
                            $imgPath = "https://ui-avatars.com/api/?name=" . urlencode($row['full_name']) . "&background=random";
                        }
                ?>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group">
                    <div class="relative h-48 overflow-hidden bg-gray-100">
                        <img src="<?php echo $imgPath; ?>" alt="<?php echo htmlspecialchars($row['full_name']); ?>" 
                             class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-xs font-bold text-indigo-600 shadow-sm">
                            <?php echo htmlspecialchars($row['subject']); ?>
                        </div>
                    </div>
                    
                    <div class="p-5">
                        <h3 class="font-bold text-gray-800 text-lg mb-1 truncate"><?php echo htmlspecialchars($row['full_name']); ?></h3>
                        <p class="text-gray-500 text-xs mb-4 line-clamp-2 h-8"><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <div class="flex gap-2 pt-3 border-t border-gray-100">
                            <a href="delete_teacher.php?id=<?php echo $row['teacher_id']; ?>" 
                               onclick="return confirm('Are you sure you want to remove this teacher?');"
                               class="flex-1 bg-red-50 text-red-600 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition text-center">
                                <i class="fas fa-trash-alt mr-1"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>

                <?php 
                    }
                } else {
                ?>
                <div class="col-span-full flex flex-col items-center justify-center py-12 text-gray-400 bg-white rounded-xl border border-dashed border-gray-300">
                    <i class="fas fa-chalkboard-teacher text-4xl mb-3"></i>
                    <p>No teachers added yet.</p>
                </div>
                <?php } ?>

            </div>

        </main>
    </div>

</body>
</html>