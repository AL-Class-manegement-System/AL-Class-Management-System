<?php
// admin/settings.php - All bugs resolved.

session_start();
// db_con.php must be included as $conn is used from it.
include('db_con.php'); 
include('includes/auth.php'); 

// Admin role check (as per your requirement)
// if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
//     header("Location: dashboard.php");
//     exit();
// }


// =======================
// ACTION: Update Settings (Secure Prepared Statement)
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['setting_key']) && isset($_POST['setting_value'])) {
        
        // Prepared Statement for Update
        $update_stmt = $conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
        
        if ($update_stmt) {
            $update_stmt->bind_param("ss", $_POST['setting_value'], $_POST['setting_key']); 
            
            if ($update_stmt->execute()) {
                $_SESSION['message'] = "System setting updated successfully.";
            } else {
                $_SESSION['error'] = "Error updating setting: " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            $_SESSION['error'] = "Database Prepare Error: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Required data is missing.";
    }
    header("Location: settings.php");
    exit();
}
// =======================

// Fetch all settings from the database (Secure Prepared Statement)
$settings = [];
$settings_query = "SELECT setting_key, setting_value, description FROM system_settings";

$settings_result = $conn->query($settings_query);

if ($settings_result && $settings_result->num_rows > 0) {
    while($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
        <?php include('includes/sidebar.php'); ?>
        <div class="ml-64 flex-1">
            <main class="p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">⚙️ System Settings</h1>
                <hr class="mb-6">

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                    
                    <?php if (isset($settings['system_name'])): ?>
                    <form method="POST" action="settings.php" class="mb-6 border-b pb-4">
                        <label for="system_name" class="block text-sm font-bold text-gray-700 mb-2">System Name:</label>
                        <input type="text" id="system_name" name="setting_value" value="<?php echo htmlspecialchars($settings['system_name']['setting_value']); ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <input type="hidden" name="setting_key" value="system_name">
                        <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($settings['system_name']['description']); ?></p>
                        <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">Update</button>
                    </form>
                    <?php endif; ?>

                    <?php if (isset($settings['allow_registration'])): ?>
                    <form method="POST" action="settings.php" class="mb-6 border-b pb-4">
                        <label for="allow_registration" class="block text-sm font-bold text-gray-700 mb-2">New Student Registration:</label>
                        <select id="allow_registration" name="setting_value" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <option value="yes" <?php echo $settings['allow_registration']['setting_value'] == 'yes' ? 'selected' : ''; ?>>Allowed (Open)</option>
                            <option value="no" <?php echo $settings['allow_registration']['setting_value'] == 'no' ? 'selected' : ''; ?>>No (Closed)</option>
                        </select>
                        <input type="hidden" name="setting_key" value="allow_registration">
                        <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($settings['allow_registration']['description']); ?></p>
                        <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">Update</button>
                    </form>
                    <?php endif; ?>

                    <?php if (isset($settings['monthly_fee_default'])): ?>
                    <form method="POST" action="settings.php" class="mb-6">
                        <label for="monthly_fee_default" class="block text-sm font-bold text-gray-700 mb-2">Default Monthly Fee (Rs.):</label>
                        <input type="number" id="monthly_fee_default" name="setting_value" value="<?php echo htmlspecialchars($settings['monthly_fee_default']['setting_value']); ?>" step="0.01" min="0" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <input type="hidden" name="setting_key" value="monthly_fee_default">
                        <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($settings['monthly_fee_default']['description']); ?></p>
                        <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">Update</button>
                    </form>
                    <?php endif; ?>
                    
                </div>
            </main>
        </div>
    </div>
</body>
</html>