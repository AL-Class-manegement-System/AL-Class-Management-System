<?PHP include('../includes/header.php');?>

        <div class="flex flex-col-reverse lg:flex-row items-center gap-12">
            
            <!-- Hero Text -->
            <div class="w-full lg:w-1/2 text-center lg:text-left z-10">
                <div class="inline-block px-4 py-2 rounded-full bg-indigo-100 text-indigo-700 text-sm font-semibold mb-6 hero-badge opacity-0">
                    <i class="fas fa-trophy mr-2 text-yellow-500"></i> #1 A/L Institute in Sri Lanka
                </div>
                <h1 class="text-4xl lg:text-6xl font-bold leading-tight mb-6 hero-title opacity-0">
                    Your Journey at Our <span class="text-indigo-600">Institute</span> Starts Here
                </h1>
                <p class="text-lg text-gray-600 mb-8 hero-desc opacity-0">
                    Master Physics, Chemistry, Maths & Biology with the island's best lecturers. Comprehensive Theory, Revision & Paper classes.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start hero-buttons opacity-0">
                    <a href="student_register.html" class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-bold shadow-lg hover:bg-indigo-700 hover:shadow-indigo-500/40 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        Register Now <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="#" class="px-8 py-4 bg-white text-gray-700 border border-gray-200 rounded-xl font-bold hover:bg-gray-50 hover:border-gray-300 transition flex items-center justify-center gap-2">
                        <i class="fas fa-calendar-alt text-indigo-500 text-xl"></i> View Timetable
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="mt-12 flex justify-center lg:justify-start gap-8 border-t border-gray-200 pt-8 hero-stats opacity-0">
                    <div>
                        <span class="block text-3xl font-bold text-gray-800">500+</span>
                        <span class="text-sm text-gray-500">Campus Selects</span>
                    </div>
                    <div>
                        <span class="block text-3xl font-bold text-gray-800">50+</span>
                        <span class="text-sm text-gray-500">District Ranks</span>
                    </div>
                    <div>
                        <span class="block text-3xl font-bold text-gray-800">10+</span>
                        <span class="text-sm text-gray-500">Years Excellence</span>
                    </div>
                </div>
            </div>

            <!-- Hero Image / Visual -->
            <div class="w-full lg:w-1/2 relative hero-img-container opacity-0">
                <div class="relative z-10 w-full max-w-md mx-auto">
                    <!-- Abstract Education Composition using Icons/CSS -->
                    <div class="aspect-square bg-gradient-to-br from-indigo-100 to-white rounded-3xl shadow-2xl flex items-center justify-center border border-white/50 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-100 rounded-bl-full z-0"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-blue-100 rounded-tr-full z-0"></div>
                        
                        <!-- Background math symbols -->
                        <i class="fas fa-square-root-alt text-8xl text-indigo-600/10 absolute top-10 left-10"></i>
                        <i class="fas fa-atom text-9xl text-indigo-600/20 absolute bottom-10 right-10"></i>
                        
                        <div class="relative z-10 text-center">
                            <div class="w-24 h-24 bg-white rounded-2xl shadow-xl flex items-center justify-center mx-auto mb-4 text-indigo-600 text-4xl animate-bounce">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">A/L 2026</h3>
                            <p class="text-sm text-gray-500">Science | Maths | Tech</p>
                        </div>

                        <!-- Floating Badge 1 (Physics) -->
                        <div class="absolute top-10 left-4 bg-white p-3 rounded-xl shadow-lg flex items-center gap-3 animate-pulse">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                <i class="fas fa-atom"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Subject</div>
                                <div class="font-bold text-sm">Physics</div>
                            </div>
                        </div>

                        <!-- Floating Badge 2 (Combined Maths) -->
                        <div class="absolute bottom-10 right-4 bg-white p-3 rounded-xl shadow-lg flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center text-orange-600">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Subject</div>
                                <div class="font-bold text-sm">Comb. Maths</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-20 relative z-10">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 section-header opacity-0">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">Our Methodology</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">We use a proven system to ensure you get the best Z-Score.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="glass-card p-8 rounded-2xl text-center feature-card opacity-0 translate-y-10">
                    <div class="w-16 h-16 mx-auto bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl mb-6">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Theory & Revision</h3>
                    <p class="text-gray-600">Complete syllabus coverage with special revision sessions targeting difficult areas.</p>
                </div>

                <!-- Card 2 -->
                <div class="glass-card p-8 rounded-2xl text-center feature-card opacity-0 translate-y-10">
                    <div class="w-16 h-16 mx-auto bg-pink-100 text-pink-600 rounded-full flex items-center justify-center text-2xl mb-6">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Model Papers</h3>
                    <p class="text-gray-600">Regular unit tests and island-wide mock exams to prepare you for the real challenge.</p>
                </div>

                <!-- Card 3 -->
                <div class="glass-card p-8 rounded-2xl text-center feature-card opacity-0 translate-y-10">
                    <div class="w-16 h-16 mx-auto bg-cyan-100 text-cyan-600 rounded-full flex items-center justify-center text-2xl mb-6">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Progress Tracking</h3>
                    <p class="text-gray-600">Individual attention and performance tracking to improve your Z-Score systematically.</p>
                </div>
            </div>
        </div>
    </section>
<?php include('../includes/footer.php');?>
    
