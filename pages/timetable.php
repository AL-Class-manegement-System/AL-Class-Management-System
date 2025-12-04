<?php 

include('../includes/connection.php'); 
include('../includes/header.php'); 
?>

<div class="min-h-screen bg-slate-50 pb-20">
    
    <div class="relative pt-32 pb-12 px-6 bg-white shadow-sm mb-12">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4 animate-fade-up">
                Class <span class="text-indigo-600">Schedule</span>
            </h1>
            <p class="max-w-2xl mx-auto text-gray-500 animate-fade-up">
                Find the perfect class time that fits your schedule. We offer comprehensive coverage for all streams.
            </p>
        </div>
        <div class="absolute top-10 left-10 w-20 h-20 bg-indigo-100 rounded-full blur-xl opacity-50"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-pink-100 rounded-full blur-xl opacity-50"></div>
    </div>

    <div class="container mx-auto px-6">
        
        <?php
    
        $streams = ['Physical Science', 'Bio Science', 'Technology', 'Commerce', 'Arts'];
        $has_classes = false;

        foreach($streams as $stream) {
            // එක් එක් Stream එකට අදාළ Active පන්ති තෝරා ගැනීම
            // දින පිළිවෙලට (Monday to Sunday) සැකසීමට FIELD function එක භාවිතා කරයි
            $sql = "SELECT * FROM classes 
                    WHERE stream = '$stream' AND status = 1 
                    ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), time ASC";
            
            $result = $conn->query($sql);
            
            if($result->num_rows > 0) {
                $has_classes = true;
        ?>
            
            <div class="mb-16 animate-fade-up">
                <div class="flex items-center gap-3 mb-8">
                    <div class="h-8 w-1.5 bg-indigo-600 rounded-full"></div>
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo $stream; ?></h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    
                    <?php while($row = $result->fetch_assoc()) { 
                        // Stream එක අනුව කාඩ් එකේ පාට වෙනස් කිරීම (Optional Styling)
                        $card_color = 'indigo';
                        if($stream == 'Bio Science') $card_color = 'green';
                        if($stream == 'Commerce') $card_color = 'blue';
                        if($stream == 'Arts') $card_color = 'pink';
                        if($stream == 'Technology') $card_color = 'purple';
                    ?>
                        
                        <div class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                            
                            <div class="absolute top-0 left-0 w-full h-1.5 bg-<?php echo $card_color; ?>-500"></div>

                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800 group-hover:text-<?php echo $card_color; ?>-600 transition-colors">
                                        <?php echo htmlspecialchars($row['subject']); ?>
                                    </h3>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mt-1">
                                        <?php echo htmlspecialchars($row['class_name']); ?>
                                    </p>
                                </div>
                                <div class="bg-<?php echo $card_color; ?>-50 text-<?php echo $card_color; ?>-700 font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm border border-<?php echo $card_color; ?>-100">
                                    <?php echo substr($row['day'], 0, 3); ?>
                                </div>
                            </div>

                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-8 flex justify-center"><i class="fas fa-user-tie text-<?php echo $card_color; ?>-400"></i></div>
                                    <span class="font-medium"><?php echo htmlspecialchars($row['teacher_name']); ?></span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-8 flex justify-center"><i class="far fa-clock text-<?php echo $card_color; ?>-400"></i></div>
                                    <span><?php echo htmlspecialchars($row['time']); ?></span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-8 flex justify-center"><i class="fas fa-tag text-<?php echo $card_color; ?>-400"></i></div>
                                    <span class="font-bold text-gray-800">Rs. <?php echo number_format($row['fee'], 2); ?></span>
                                </div>
                            </div>

                            <a href="../log/registration.php" class="block w-full py-2.5 rounded-xl bg-gray-50 text-gray-700 font-semibold text-center text-sm hover:bg-<?php echo $card_color; ?>-600 hover:text-white transition-all duration-300">
                                Register Now <i class="fas fa-arrow-right ml-1 text-xs opacity-70"></i>
                            </a>

                        </div>

                    <?php } ?>
                    
                </div>
            </div>

        <?php 
            } // End of if
        } // End of foreach loop

        // කිසිම පන්තියක් නැත්නම් පණිවිඩයක් පෙන්වීම
        if(!$has_classes) {
            echo '
            <div class="flex flex-col items-center justify-center py-20 text-center animate-fade-up">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400 text-3xl">
                    <i class="far fa-calendar-times"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700">No Classes Scheduled Yet</h3>
                <p class="text-gray-500 mt-2">Please check back later for the updated timetable.</p>
            </div>';
        }
        ?>

    </div>
</div>

<style>
    /* Simple fade up animation */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up {
        animation: fadeUp 0.8s ease-out forwards;
    }
</style>

<?php include('../includes/footer.php'); ?>