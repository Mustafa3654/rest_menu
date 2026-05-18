<?php
include "connection.php";
start_secure_session();
$isAdmin = $_SESSION["isAdmin"] ?? false;

// Fetch restaurant settings (cached)
$settings = get_settings();

$restaurantName = $settings['restaurant_name'] ?? 'Restaurant Menu';
$restaurantLogo = $settings['restaurant_logo'] ?? '';
$restaurantPhone = $settings['restaurant_phone'] ?? 'xxxxxxxx';

// Olive Green Theme Colors
$primaryColor = '#42522B';
$accentColor  = '#42522B';
$rgb          = '66, 82, 43';
$accentRgb    = '203, 181, 139'; // Gold Glow
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurantName); ?></title>
    
    <!-- Tailwind CSS (replaces Bootstrap) -->
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>style/tailwind.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar-custom">
        <div class="max-w-6xl mx-auto px-4 flex items-center justify-between lg:justify-normal relative">
            <!-- Left Placeholder for centering balance on mobile -->
            <div class="hidden max-lg:block" style="width: 80px; flex-shrink: 0;"></div>

            <!-- Centered Brand: Logo + Name Below -->
            <a class="flex flex-col items-center no-underline mx-auto lg:mx-0" href="<?php echo $BASE_URL; ?>index" style="text-decoration: none;">
                <img src="<?php echo $BASE_URL . htmlspecialchars($restaurantLogo); ?>" alt="Logo" style="height: 45px; width: auto; border-radius: 6px; transition: transform 0.3s ease;">
                <span class="brand-name" style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 700; color: var(--color-olive); margin-top: 4px; text-align: center; white-space: nowrap; transition: color 0.3s ease;"><?php echo htmlspecialchars($restaurantName); ?></span>
            </a>

            <!-- Right Controls: Theme Toggle & Burger -->
            <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                <div class="theme-toggle" id="theme-toggle" title="Toggle Dark/Light Mode">
                    <i class="fas fa-moon"></i>
                </div>
                <button id="mobile-menu-btn" class="lg:hidden flex items-center justify-center p-2 rounded-lg border" style="background: var(--color-card); border-color: var(--color-border); color: var(--color-text); cursor: pointer;">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Navigation Links -->
            <div id="navbarNav" class="hidden max-lg:absolute max-lg:top-full max-lg:left-0 max-lg:w-full max-lg:bg-[var(--color-header-bg,var(--color-bg))] max-lg:p-5 max-lg:shadow-lg max-lg:z-[1000] lg:flex lg:items-center lg:ml-auto">
                <ul class="flex flex-col lg:flex-row lg:items-center list-none m-0 p-0 max-lg:text-right">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $BASE_URL; ?>index">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $BASE_URL; ?>menu">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $BASE_URL; ?>contact">Contact</a></li>
                    <?php if ($isAdmin): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $BASE_URL; ?>login">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="<?php echo $BASE_URL; ?>menu" class="inline-block px-6 py-3 no-underline font-bold text-white rounded-full btn-order-custom max-lg:block max-lg:text-center" style="background: var(--color-accent);">
                            Order Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
	    
    <!-- Mobile Menu Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('mobile-menu-btn');
        var menu = document.getElementById('navbarNav');
        if (btn && menu) {
            btn.addEventListener('click', function() {
                menu.classList.toggle('hidden');
            });
            // Close menu when clicking a link (on mobile)
            menu.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        menu.classList.add('hidden');
                    }
                });
            });
        }
    });
    </script>
    
    <!-- External Theme JS -->
    <script src="<?php echo $BASE_URL; ?>JS/theme.js"></script>
    <main>

