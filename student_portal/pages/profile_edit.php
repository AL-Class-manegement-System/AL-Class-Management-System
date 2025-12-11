<?php
include('../includes/student_header.php');

?>



<div class="container mx-auto p-4 lg:p-8 max-w-4xl">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Account Settings</h1>
        <p class="text-gray-500 mt-1">Manage your profile details and security.</p>
    </div>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm p-6 text-center border border-gray-100">
                    <div class="relative w-32 h-32 mx-auto mb-4 group">
                        <img src="https://ui-avatars.com/api/?name=User&background=0D8ABC&color=fff" alt="Profile"
                            class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg">

                        <div
                            class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 cursor-pointer">
                            <i class="fas fa-camera text-white text-2xl"></i>
                        </div>
                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            name="profile_pic">
                    </div>

                    <h2 class="text-xl font-bold text-gray-900">John Doe</h2>
                    <p class="text-sm text-gray-500 mb-4">Student / Developer</p>

                    <div class="border-t pt-4">
                        <button type="button" class="text-red-500 hover:text-red-700 text-sm font-medium">
                            <i class="fas fa-trash-alt mr-1"></i> Remove Photo
                        </button>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Personal Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" value="John"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="Enter first name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" value="Doe"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="Enter last name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" value="+94 77 123 4567"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="Enter phone number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" value="johndoe@example.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50"
                            readonly>
                        <p class="text-xs text-gray-500 mt-1">Email cannot be changed contact admin.</p>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                        <textarea rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            placeholder="Tell something about yourself..."></textarea>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Change Password</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="********">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                    placeholder="********">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                    placeholder="********">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="button"
                        class="px-6 py-2.5 rounded-lg text-gray-700 font-medium hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                        Save Changes
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>



<?php
include('../includes/footer.php');

?>