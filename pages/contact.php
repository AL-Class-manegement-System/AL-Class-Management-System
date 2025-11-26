<?php include('../includes/header.php'); ?>
<!-- Spacer to create gap below header (adjust height as needed) -->
<div class="h-16 md:h-20 lg:h-24" aria-hidden="true"></div>
<main class="flex-grow py-10 p-10 px-4">
        <!-- Contact Container -->
        <div class="max-w-[1200px] mx-auto bg-white shadow-[0_0_10px_rgba(0,0,0,0.1)] p-8 md:p-10 rounded-sm">
            
            <section class="contact-us">
                <h1 class="text-center text-[2.5em] text-[#333] mb-2.5">Contact Us</h1>
                <p class="text-center text-[#666] mb-10">You can contact us through the following:</p>

                <!-- Content Wrapper -->
                <div class="flex flex-col md:flex-row gap-10 pt-5">
                    
                    <!-- Form Column -->
                    <div class="flex-1">
                        <form action="submit_form.php" method="POST" class="w-full">
                            <label for="name" class="block mt-4 font-bold text-[#555]">Name:</label>
                            <input type="text" id="name" name="name" required placeholder="Name" 
                                class="w-full p-2.5 mt-1.5 mb-4 border border-[#ddd] rounded focus:outline-none focus:border-[#0c0647] transition-colors">

                            <label for="email" class="block mt-4 font-bold text-[#555]">Email:</label>
                            <input type="email" id="email" name="email" required placeholder="Email" 
                                class="w-full p-2.5 mt-1.5 mb-4 border border-[#ddd] rounded focus:outline-none focus:border-[#0c0647] transition-colors">

                            <label for="message" class="block mt-4 font-bold text-[#555]">Message:</label>
                            <textarea id="message" name="message" rows="6" required placeholder="Message" 
                                class="w-full p-2.5 mt-1.5 mb-4 border border-[#ddd] rounded resize-y focus:outline-none focus:border-[#0c0647] transition-colors"></textarea>

                            <button type="submit" 
                                class="w-full bg-[#0c0647] text-white py-4 px-8 rounded cursor-pointer text-[1.1em] font-bold mt-5 hover:bg-[#23136b] transition-colors duration-300">
                                Send
                            </button>
                        </form>
                    </div>

                    <!-- Office Details Column -->
                    <div class="flex-1 md:pl-10 md:border-l border-[#eee]">
                        <p class="font-bold text-[#05943c] mb-1.5">FUTUREMINDS.lk</p>
                        <h2 class="text-[2em] leading-[1.2] mt-0 mb-5 font-bold text-gray-800">Our Head Office <br> At Rajagiriya</h2>

                        <div class="space-y-2.5 text-[#333]">
                            <p>No: 160, Buthgamuwa Road, Kalapaluwawa, Rajagiriya.</p>
                            <p>075 444 4444</p>
                            <p>info@futureminds.lk</p>
                            <p>Monday-Saturday : 8am-5pm</p>
                        </div>

                        <!-- Social Icons -->
                        <div class="mt-4 flex gap-2.5">
                            <a href="#" aria-label="Facebook" class="w-[30px] h-[30px] flex items-center justify-center border border-[#ccc] rounded-full text-[#666] transition-colors hover:bg-[#f0f0f0] hover:text-black">
                                <i class="fab fa-facebook-f text-sm"></i>
                            </a>
                            <a href="#" aria-label="Twitter" class="w-[30px] h-[30px] flex items-center justify-center border border-[#ccc] rounded-full text-[#666] transition-colors hover:bg-[#f0f0f0] hover:text-black">
                                <i class="fab fa-twitter text-sm"></i>
                            </a>
                            <a href="#" aria-label="LinkedIn" class="w-[30px] h-[30px] flex items-center justify-center border border-[#ccc] rounded-full text-[#666] transition-colors hover:bg-[#f0f0f0] hover:text-black">
                                <i class="fab fa-linkedin-in text-sm"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </main>
<?php include('../includes/footer.php'); ?>