<?php 
// DB Connection එක සහ Header එක
// ඔබේ ෆයිල් ස්ට්‍රක්චර් එක අනුව path එක වෙනස් විය හැක. (උදා: include('../db_con.php');)
include 'db_con.php'; 
include('../includes/header.php'); 
?>

<div class="p-3">
    
    <div class="flex items-center mt-20 w-full p-2 text-orange-500 p-6 backdrop-blur-sm bg-orange-50 opacity-100 rounded-2xl text-xl font-bold font-10 mb-5 outline-[#243c5a] shadow-lg text-shadow-lg border-solid">
        <h1>Our Expert Teachers</h1>
    </div>

    <div class="grid w-full grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-4">

        <?php
        // Database එකෙන් teachers ලා ඔක්කොම ගන්න Query එක
        $sql = "SELECT * FROM teachers ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);

        // Teachers ලා ඉන්නවා නම් Loop එක run වෙනවා
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                
                // Image Path එක
                // 'assests' නෙවෙයි 'assets' ලෙස නිවැරදි කර ඇත.
                $imagePath = "../assets/images/teachers/" . $row['image'];
        ?>

        <div class="group relative w-full h-[450px]">
            <div class="absolute -inset-1 bg-gradient-to-r from-pink-600 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200">
            </div>

            <div class="relative w-full h-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col transition-all duration-300 transform group-hover:-translate-y-2 group-hover:shadow-2xl ring-1 ring-slate-900/5">
                
                <div class="h-56 overflow-hidden relative">
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $row['full_name']; ?>"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        onerror="this.src='../assets/images/default_teacher.png'"> <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm p-2 rounded-full shadow-sm">
                        <i data-lucide="heart" class="w-5 h-5 text-rose-500 fill-rose-500/20"></i>
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-1 justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 text-xs font-semibold text-purple-600 bg-purple-50 rounded-md uppercase tracking-wider">
                                <?php echo $row['subject']; ?>
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-purple-600 transition-colors">
                            <?php echo $row['full_name']; ?>
                        </h3>
                        
                        <p class="text-slate-500 text-sm leading-relaxed line-clamp-3">
                            <?php echo $row['description']; ?>
                        </p>
                    </div>

                    <button class="mt-4 w-full py-3 rounded-lg bg-slate-900 text-white font-medium text-sm flex items-center justify-center gap-2 transition-all duration-300 hover:bg-purple-600 group-hover:translate-x-1">
                        View Profile <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php 
            } // End of While Loop
        } else {
            echo "<p class='text-center text-gray-500 col-span-3'>No teachers added yet.</p>";
        }
        ?>

    </div>
</div>

<?php include('../includes/footer.php'); ?>