<?php
include "connection.php"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isAdmin = $_SESSION["isAdmin"] ?? false;

// Fetch restaurant settings
$settingsQuery = "SELECT * FROM settings LIMIT 1";
$settingsResult = $conn->query($settingsQuery);
$settings = $settingsResult ? $settingsResult->fetch_assoc() : null;

$restaurantName = $settings['restaurant_name'] ?? 'Restaurant Menu';
$restaurantLogo = $settings['restaurant_logo'] ?? 'bgs/logoo.jfif';
$restaurantPhone = $settings['restaurant_phone'] ?? '03 495 894';
$themeColor = $settings['theme_color'] ?? '#1a2a6c';
list($r, $g, $b) = sscanf($themeColor, "#%02x%02x%02x");
$themeRGB = "$r, $g, $b";

// Calculate a light version for buttons/glows (mix 40% white)
$lightR = (int)($r + (255 - $r) * 0.4);
$lightG = (int)($g + (255 - $g) * 0.4);
$lightB = (int)($b + (255 - $b) * 0.4);
$themeLightHex = sprintf("#%02x%02x%02x", $lightR, $lightG, $lightB);
$themeLightRGB = "$lightR, $lightG, $lightB";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurantName); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Global Header CSS -->
    <link rel="stylesheet" href="style/header.css">

    <!-- Dynamic Theme Color -->
    <style>
        :root {
            --primary-blue: <?php echo htmlspecialchars($themeColor); ?> !important;
            --accent-blue: <?php echo htmlspecialchars($themeLightHex); ?> !important;
        }
        
        /* Overrides for hardcoded RGBA gradients/shadows using the theme and light variant */
        .hero-overlay {
            background: linear-gradient(
                135deg,
                rgba(15, 23, 42, 0.9) 0%,
                rgba(<?php echo $themeRGB; ?>, 0.8) 100%
            ) !important;
        }
        .menu-header-overlay {
            background: rgba(<?php echo $themeRGB; ?>, 0.6) !important;
        }

        /* Elements using the light variant */
        .btn-order, .add-btn, .category-tab.active {
            background: <?php echo htmlspecialchars($themeLightHex); ?> !important;
            color: white !important;
        }
        
        .btn-hero-primary {
            box-shadow: 0 10px 20px rgba(<?php echo $themeLightRGB; ?>, 0.3) !important;
            background: <?php echo htmlspecialchars($themeLightHex); ?> !important;
        }
        .btn-hero-primary:hover {
            box-shadow: 0 15px 30px rgba(<?php echo $themeLightRGB; ?>, 0.4) !important;
            background: <?php echo htmlspecialchars($themeColor); ?> !important;
        }

        .category-tab.active, .menu-card.in-cart {
            border-color: <?php echo htmlspecialchars($themeLightHex); ?> !important;
        }
        .category-tab:hover {
            color: <?php echo htmlspecialchars($themeLightHex); ?> !important;
        }
        
        .btn-order:hover, .add-btn:hover {
            background: <?php echo htmlspecialchars($themeColor); ?> !important;
        }
        
        .info-box.alt .info-box-icon {
            box-shadow: 0 8px 16px rgba(<?php echo $themeLightRGB; ?>, 0.2) !important;
            background: <?php echo htmlspecialchars($themeLightHex); ?> !important;
        }

        @keyframes borderGlow {
            0%   { box-shadow: 0 0 5px rgba(<?php echo $themeLightRGB; ?>, 0.5), 0 0 10px rgba(<?php echo $themeLightRGB; ?>, 0.3); border-color: rgba(<?php echo $themeLightRGB; ?>, 0.5); }
            50%  { box-shadow: 0 0 15px rgba(<?php echo $themeLightRGB; ?>, 0.9), 0 0 25px rgba(<?php echo $themeLightRGB; ?>, 0.5); border-color: rgba(<?php echo $themeLightRGB; ?>, 1); }
            100% { box-shadow: 0 0 5px rgba(<?php echo $themeLightRGB; ?>, 0.5), 0 0 10px rgba(<?php echo $themeLightRGB; ?>, 0.3); border-color: rgba(<?php echo $themeLightRGB; ?>, 0.5); }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="<?php echo htmlspecialchars($restaurantLogo); ?>" alt="Logo">
                <span><?php echo htmlspecialchars($restaurantName); ?></span>
            </a>
            

			<div class="theme-toggle ms-lg-3" id="theme-toggle" title="Toggle Dark/Light Mode">
	            <i class="fas fa-moon"></i>
	        </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Dashboard</a></li>
	                    <li class="nav-item">
	                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $restaurantPhone); ?>" class="btn btn-order ms-lg-3" target="_blank">
	                            Order Now
	                        </a>
	                    </li>
                        <br>
	                </ul>
	            </div>
	        </div>
	    </nav>
	    
	    <script>
	        // Dark Mode Toggle Logic
	        const themeToggle = document.getElementById('theme-toggle');
	        const body = document.body;
	        const icon = themeToggle.querySelector('i');
	
	        // Check for saved theme preference
	        const currentTheme = localStorage.getItem('theme');
	        if (currentTheme === 'dark') {
	            body.classList.add('dark-mode');
	            icon.classList.replace('fa-sun', 'fa-moon');
	        }
	
	        themeToggle.addEventListener('click', () => {
	            body.classList.toggle('dark-mode');
	            
	            if (body.classList.contains('dark-mode')) {
	                icon.classList.replace('fa-sun', 'fa-moon');
	                localStorage.setItem('theme', 'dark');
	            } else {
	                icon.classList.replace('fa-moon', 'fa-sun');
	                localStorage.setItem('theme', 'light');
	            }
	        });
	    </script>
    <main>
