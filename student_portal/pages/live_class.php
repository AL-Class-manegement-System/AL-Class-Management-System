<?php
include('../includes/student_header.php');


?>

<div class="flex-1 flex flex-col h-screen overflow-y-auto">

    <main class="p-8 pt-2">

        <div
            class="bg-gradient-to-r from-green-600 to-teal-600 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden opacity-80">
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Live Class Sessions 🎥
                </h1>
                <p class="opacity-90">Join your live class session below. Make sure to be on time and ready to learn!</p>
            </div>  
            <i class="fas fa-video absolute -bottom-4 -right-4 text-9xl text-white opacity-10 transform rotate-12"></i>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Current Live Class</h2>
            <div class="aspect-w-16 aspect-h-9">
                <iframe
                    src="@"
                    title="Live Class Session"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="w-full h-full rounded-lg">
                </iframe>
            </div>
            <!-- <div class="mt-4">
                <h3 class="text-xl font-semibold">Subject: Introduction to Web Development</h3>
                <p class="text-gray-600">Instructor: John Doe</p>
                <p class="text-gray-600">Time: 10:00 AM - 11:30 AM</p>
            </div> -->
        </div>
    </main>
</div>