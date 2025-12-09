<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Path variable check
if (!isset($path)) { $path = "../"; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - AL Management</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="<?php echo $path; ?>style/style.css">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-gray-50 font-sans antialiased"></body>