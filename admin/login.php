<?php
session_start();
include 'db_con.php'; // Database connection

// Variable to display error message
$error = "";

if (isset($_POST['login_btn'])) {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 1. Database Connection Check
    if ($conn->connect_error) {
        $error = "Connection Failed: " . $conn->connect_error;
    } else {
        // 2. Find the user (Using Prepared Statement)
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'Admin' LIMIT 1");

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // 3. Password Check (NOTE: Password should be hashed in a real application)
                if ($password === $user['password']) {
                    
                    // Successful Login, set session variables
                    $_SESSION['admin_id'] = $user['user_id'];
                    $_SESSION['admin_name'] = $user['username'];
                    $_SESSION['is_admin_logged_in'] = true;

                    // Redirect to Dashboard
                    header("Location: index.php");
                    exit();

                } else {
                    $error = "Incorrect Password!";
                }
            } else {
                $error = "User Not Found or Not an Admin!";
            }
            $stmt->close();
        } else {
            $error = "Database Query Error!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Future Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Admin Login</h2>
            <p class="text-gray-500 text-sm">Please sign in to continue</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition" placeholder="Enter username">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition" placeholder="Enter password">
            </div>

            <button type="submit" name="login_btn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                Sign In
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="../index.php" class="text-sm text-gray-400 hover:text-indigo-600 transition">Back to Home</a>
        </div>
    </div>

</body>
</html>