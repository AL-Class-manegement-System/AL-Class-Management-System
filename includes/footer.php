<footer class="bg-slate-900 text-slate-300 py-16 relative overflow-hidden mt-20">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-50"></div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            
            <div class="space-y-4">
                <div class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-university text-indigo-500"></i>
                    <span>Future Minds</span>
                </div>
                <p class="text-sm leading-relaxed text-slate-400">
                    Empowering the next generation of leaders with world-class education in Science, Mathematics, and Technology. Join us to shape your future.
                </p>
                <div class="flex space-x-4 pt-2">
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition duration-300">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-pink-600 hover:text-white transition duration-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-red-600 hover:text-white transition duration-300">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-white font-semibold text-lg mb-6">Quick Links</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="../pages/index.php" class="hover:text-indigo-400 transition-colors duration-200"><i class="fas fa-chevron-right text-xs text-indigo-500 mr-2"></i>Home</a></li>
                    <li><a href="../pages/Streams.php" class="hover:text-indigo-400 transition-colors duration-200"><i class="fas fa-chevron-right text-xs text-indigo-500 mr-2"></i>Streams</a></li>
                    <li><a href="../pages/timetable.php" class="hover:text-indigo-400 transition-colors duration-200"><i class="fas fa-chevron-right text-xs text-indigo-500 mr-2"></i>Class Timetable</a></li>
                    <li><a href="#" class="hover:text-indigo-400 transition-colors duration-200"><i class="fas fa-chevron-right text-xs text-indigo-500 mr-2"></i>Exam Results</a></li>
                    <li><a href="../log/login.php" class="hover:text-indigo-400 transition-colors duration-200"><i class="fas fa-chevron-right text-xs text-indigo-500 mr-2"></i>LMS Login</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold text-lg mb-6">Contact Us</h3>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-indigo-500 mt-1"></i>
                        <span>No. 123, High Level Road,<br>Nugegoda, Sri Lanka</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone-alt text-indigo-500"></i>
                        <span>+94 77 123 4567</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-indigo-500"></i>
                        <span>info@futureminds.lk</span>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold text-lg mb-6">Newsletter</h3>
                <p class="text-sm text-slate-400 mb-4">Subscribe to get the latest updates on class schedules and special seminars.</p>
                
                <form class="space-y-3" action="../includes/subscribe_process.php" method="POST">
                    <input type="email" name="email" required placeholder="Your Email Address" class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    <button type="submit" name="subscribe_btn" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition shadow-lg shadow-indigo-500/20">
                        Subscribe Now
                    </button>
                </form>
            </div>

        </div>

        <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <p>&copy; <?php echo date("Y"); ?> Future Minds A/L Institute. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-indigo-400 transition">Privacy Policy</a>
                <a href="#" class="hover:text-indigo-400 transition">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<script src="../js/home.js"></script>
<?php if (isset($_GET['status']) && isset($_GET['msg'])): ?>
    <div id="toast-notification" class="fixed bottom-5 right-5 z-50 transform transition-all duration-500 ease-in-out translate-y-0 opacity-100 animate-slide-in-up">
        <?php 
            $status = $_GET['status'];
            $msg = htmlspecialchars($_GET['msg']); 

            
            $bgColor = 'bg-blue-600'; 
            $icon = 'fa-info-circle';

            if ($status == 'success') {
                $bgColor = 'bg-emerald-600'; 
                $icon = 'fa-check-circle';
            } elseif ($status == 'error') {
                $bgColor = 'bg-red-600'; 
                $icon = 'fa-times-circle';
            } elseif ($status == 'warning') {
                $bgColor = 'bg-amber-500'; 
                $icon = 'fa-exclamation-triangle';
            }
        ?>
        
        <div class="<?php echo $bgColor; ?> text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-4 min-w-[320px] border border-white/10 backdrop-blur-sm">
            <i class="fas <?php echo $icon; ?> text-2xl"></i>
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wider mb-1"><?php echo ucfirst($status); ?></h4>
                <p class="text-sm font-medium opacity-95"><?php echo $msg; ?></p>
            </div>
            <button onclick="closeToast()" class="ml-auto text-white/60 hover:text-white transition transform hover:scale-110">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        //2 se
        setTimeout(() => {
            closeToast();
        }, 2000);

        function closeToast() {
            const toast = document.getElementById('toast-notification');
            if (toast) {
               
                toast.classList.add('translate-y-full', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }
        }
        
      
        if (window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.delete('status');
            url.searchParams.delete('msg');
            window.history.replaceState(null, '', url.toString());
        }
    </script>
<?php endif; ?>

<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/693c0f6c382879197d6b9480/1jc99ogor';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
</body>
</html>