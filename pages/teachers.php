<?php 

include('../includes/connection.php'); 
include('../includes/header.php'); 
?>

<div class="min-h-screen bg-gray-50/50 pb-20">
    
    <div class="relative pt-32 pb-12 px-6">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-6">
                Our <span class="text-indigo-600">Teachers</span>
            </h1>
            <p class="max-w-2xl mx-auto text-gray-500">
                Meet the expert panel dedicated to your success.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">

            <?php
      
            $sql = "SELECT * FROM teachers WHERE status = 1 ORDER BY teacher_id DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    
                    // Image validation
                    $img = $row['image'];
                    $imagePath = "../assets/images/teachers/$img";
                    
                    if (empty($img) || !file_exists("../assets/images/teachers/" . $img)) {
                        $imagePath = "https://ui-avatars.com/api/?name=" . urlencode($row['full_name']) . "&background=random";
                    }
            ?>

            <div class="group relative bg-white rounded-3xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300">
                
                <div class="relative h-72 overflow-hidden">
                    <img src="<?php echo $imagePath; ?>" 
                         alt="<?php echo $row['full_name']; ?>"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-indigo-700">
                        <?php echo $row['subject']; ?>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold text-slate-800 mb-2">
                        <?php echo $row['full_name']; ?>
                    </h3>
                    
                    <p class="text-gray-500 text-sm line-clamp-3 mb-4">
                        <?php echo $row['description']; ?>
                    </p>

                    <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Lecturer</span>
                        <a href="teacher_details_view.php" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Profile →</a>
                    </div>
                </div>
            </div>
            <?php 
                } // Loop end
            } else {
                echo '<div class="col-span-full text-center text-gray-500 py-10">No teachers added yet.</div>';
            }
            ?>

        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>