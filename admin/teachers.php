<?php include('../includes/header.php'); ?>
<?php include('../includes/connection.php'); ?>

<div class="relative pt-32 pb-20 bg-slate-50 min-h-screen">
    
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[500px] bg-gradient-to-b from-indigo-50/50 to-transparent -z-10"></div>

    <div class="container mx-auto px-6">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">
                Meet Our <span class="text-indigo-600">Experts</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Learn from the most experienced and qualified panel of lecturers in the island.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
            
            <?php
            // Fetch teachers from DB
            $sql = "SELECT * FROM teachers WHERE status = 1 ORDER BY teacher_id DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Image path fix (admin folder eke upload wena nisa assets folder eka thiyenne eka level ekak udin)
                    $image_path = "../assets/images/teachers/" . $row['image'];
            ?>

            <div class="group relative bg-white rounded-3xl p-4 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:-translate-y-2">
                
                <div class="relative h-[300px] w-full overflow-hidden rounded-2xl mb-6">
                    <img src="<?php echo $image_path; ?>" alt="<?php echo $row['full_name']; ?>"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        onerror="this.src='../assests/images/user2.jpg'"> <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-lg shadow-sm">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-wide">
                            <?php echo $row['subject']; ?>
                        </span>
                    </div>

                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>
                </div>

                <div class="px-2 pb-4">
                    <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-indigo-600 transition-colors">
                        <?php echo $row['full_name']; ?>
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-4">
                        <?php echo $row['description']; ?>
                    </p>
                    
                    <button class="w-full py-3 rounded-xl bg-gray-50 text-gray-700 font-semibold text-sm hover:bg-indigo-600 hover:text-white transition-all duration-300 flex items-center justify-center gap-2">
                        View Profile <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>

            </div>

            <?php 
                } // End While
            } else {
            ?>
                <div class="col-span-full text-center py-20">
                    <div class="inline-block p-6 rounded-full bg-indigo-50 text-indigo-300 mb-4">
                        <i class="fas fa-chalkboard-teacher text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-400">No teachers added yet.</h3>
                </div>
            <?php 
            } 
            ?>

        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>