<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin('../login');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../style/tailwind.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#F7F5EA] font-poppins min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl border border-[#CBB58B] shadow-lg p-8 text-center w-80">
        <h1 class="text-[#42522B] text-2xl font-bold mb-8">Dashboard</h1>
        <div class="flex flex-col gap-4">
            <a href="addItem" class="block py-3 px-4 rounded-lg bg-[#42522B] text-white font-bold no-underline transition-all duration-300 hover:bg-[#2b3a1d] hover:-translate-y-0.5 hover:shadow-md">Add Item</a>
            <a href="addCategory" class="block py-3 px-4 rounded-lg bg-[#42522B] text-white font-bold no-underline transition-all duration-300 hover:bg-[#2b3a1d] hover:-translate-y-0.5 hover:shadow-md">Add Category</a>
            <a href="viewItems" class="block py-3 px-4 rounded-lg bg-[#42522B] text-white font-bold no-underline transition-all duration-300 hover:bg-[#2b3a1d] hover:-translate-y-0.5 hover:shadow-md">View Items</a>
            <a href="viewCategories" class="block py-3 px-4 rounded-lg bg-[#42522B] text-white font-bold no-underline transition-all duration-300 hover:bg-[#2b3a1d] hover:-translate-y-0.5 hover:shadow-md">View Categories</a>
            <a href="editSettings" class="block py-3 px-4 rounded-lg bg-[#42522B] text-white font-bold no-underline transition-all duration-300 hover:bg-[#2b3a1d] hover:-translate-y-0.5 hover:shadow-md">Global Settings</a>
            <a href="editTelegram" class="block py-3 px-4 rounded-lg bg-[#42522B] text-white font-bold no-underline transition-all duration-300 hover:bg-[#2b3a1d] hover:-translate-y-0.5 hover:shadow-md">Telegram Settings</a>
            <a href="manageGallery" class="block py-3 px-4 rounded-lg bg-[#42522B] text-white font-bold no-underline transition-all duration-300 hover:bg-[#2b3a1d] hover:-translate-y-0.5 hover:shadow-md">Manage Gallery</a>

            <a href="<?php echo $BASE_URL; ?>index" class="block py-3 px-4 rounded-lg bg-[#6c757d] text-white font-bold no-underline transition-all duration-300 hover:bg-[#5a6268] hover:-translate-y-0.5">Back to Site</a>
            <a href="<?php echo $BASE_URL; ?>logout" class="block py-3 px-4 rounded-lg bg-[#e11d48] text-white font-bold no-underline transition-all duration-300 hover:bg-[#be123c] hover:-translate-y-0.5">Logout</a>
        </div>
    </div>
</body>
</html>
