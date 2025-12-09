<?php include('../includes/header.php'); ?>

<div class="min-h-screen bg-slate-50 relative overflow-hidden">
    
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-[100px] -z-10"></div>
    <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-pink-500/5 rounded-full blur-[80px] -z-10"></div>

    <div class="pt-32 pb-12 text-center px-6">
        <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-6 animate-fade-up">
            About <span class="text-indigo-600">Future Minds</span>
        </h1>
        <p class="max-w-3xl mx-auto text-gray-500 text-lg leading-relaxed animate-fade-up" style="animation-delay: 0.1s;">
            Shaping the leaders of tomorrow with excellence in education, innovation, and discipline.
        </p>
    </div>

    <div class="container mx-auto px-6 pb-20">
        
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-xl shadow-indigo-100/50 border border-white mb-16 animate-fade-up" style="animation-delay: 0.2s;">
            <div class="flex flex-col lg:flex-row gap-12 items-center">
                
                <div class="lg:w-1/2 space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-1 w-12 bg-indigo-600 rounded-full"></div>
                        <span class="text-indigo-600 font-bold uppercase tracking-wider text-sm">Who We Are</span>
                    </div>
                    
                    <h2 class="text-3xl font-bold text-slate-800">A Beacon of Academic Excellence</h2>
                    
                    <p class="text-gray-600 leading-relaxed text-justify">
                        Future Mind stands as a beacon of academic excellence in the Sri Lankan education landscape, steadfast within its commitment to shaping the leaders of tomorrow. As a premier educational institute, we go beyond traditional teaching methods to offer a comprehensive learning ecosystem designed specifically for Advanced Level (A/L) students.
                    </p>
                    
                    <p class="text-gray-600 leading-relaxed text-justify">
                        Backed by years of unwavering dedication and a proven track record of producing top island rankings, we provide a robust foundation for success across all major disciplines—including Physical Science, Bio-Science, Commerce, Arts, and Technology. Our mission transcends mere academic instruction; we strive to cultivate a disciplined, innovative, and resilient generation.
                    </p>

                    <div class="pt-4 flex gap-8">
                        <div>
                            <span class="block text-3xl font-bold text-indigo-600">10+</span>
                            <span class="text-sm text-gray-500">Years Experience</span>
                        </div>
                        <div>
                            <span class="block text-3xl font-bold text-indigo-600">500+</span>
                            <span class="text-sm text-gray-500">Annual Graduates</span>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2 relative">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-indigo-500/20 group">
                        <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b955?q=80&w=2070&auto=format&fit=crop" 
                             alt="About Future Minds" 
                             class="w-full h-auto object-cover transform group-hover:scale-105 transition-transform duration-700">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                        
                        <div class="absolute bottom-6 left-6 text-white">
                            <p class="font-bold text-xl">World Class Learning</p>
                            <p class="text-sm opacity-90">State-of-the-art facilities</p>
                        </div>
                    </div>
                    
                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-dots opacity-20 hidden md:block z-[-1]"></div>
                    <div class="absolute -top-6 -left-6 w-24 h-24 bg-dots opacity-20 hidden md:block z-[-1]"></div>
                </div>

            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="bg-white p-8 rounded-3xl shadow-lg shadow-blue-100/50 border border-white hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group animate-fade-up" style="animation-delay: 0.3s;">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-3xl mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-4">Our Vision</h3>
                <p class="text-gray-600 leading-relaxed">
                    "To create a brilliant future generation equipped with knowledge, skills, and values, capable of overcoming any challenge."
                </p>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-lg shadow-pink-100/50 border border-white hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group animate-fade-up" style="animation-delay: 0.4s;">
                <div class="w-16 h-16 bg-pink-50 rounded-2xl flex items-center justify-center text-pink-600 text-3xl mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-4">Our Mission</h3>
                <p class="text-gray-600 leading-relaxed">
                    "To provide every student with the necessary facilities and guidance under the mentorship of expert lecturers and modern technology, empowering them to reach their maximum potential."
                </p>
            </div>

        </div>

    </div>
</div>

<style>
    /* Custom Animations & Styles */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up {
        animation: fadeUp 0.8s ease-out forwards;
        opacity: 0;
    }
    
    .bg-dots {
        background-image: radial-gradient(#6366f1 2px, transparent 2px);
        background-size: 20px 20px;
    }
</style>

<?php include('../includes/footer.php'); ?>