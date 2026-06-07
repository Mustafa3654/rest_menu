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
    <meta name="description" content="<?php echo htmlspecialchars($settings['restaurant_description'] ?? 'Discover our authentic Mediterranean menu.'); ?>">
    <meta name="keywords" content="restaurant, menu, mediterranean, lebanese, food, <?php echo htmlspecialchars($restaurantName); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($restaurantName); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($settings['restaurant_description'] ?? 'Discover our authentic Mediterranean menu.'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . htmlspecialchars($_SERVER['HTTP_HOST'] ?? '') . htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/'); ?>">
    <?php if (!empty($restaurantLogo)): ?>
    <meta property="og:image" content="<?php echo $BASE_URL . htmlspecialchars($restaurantLogo); ?>">
    <?php endif; ?>
    <title><?php echo htmlspecialchars($restaurantName); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Global Header CSS -->
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/header.css">

    <!-- Theme Style -->
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/theme.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container d-flex align-items-center justify-content-between position-relative">
            <!-- Left Placeholder for centering balance -->
            <div class="header-placeholder d-lg-none" style="width: 80px;"></div>

            <!-- Centered Brand: Logo Only -->
            <a class="navbar-brand centered-brand d-flex flex-column align-items-center" href="<?php echo $BASE_URL; ?>index">
                <img src="<?php echo $BASE_URL . htmlspecialchars($restaurantLogo); ?>" alt="Logo">
            </a>

            <!-- Right Controls: Theme Toggle & Burger -->
            <div class="header-controls d-flex align-items-center gap-2 gap-md-3">
                <div class="theme-toggle" id="theme-toggle" title="Toggle Dark/Light Mode">
                    <i class="fas fa-moon"></i>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $BASE_URL; ?>index">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $BASE_URL; ?>about">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $BASE_URL; ?>menu">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $BASE_URL; ?>contact">Contact</a></li>
                    <?php if ($isAdmin): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $BASE_URL; ?>login">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="<?php echo $BASE_URL; ?>menu" class="btn btn-order ms-lg-3">
                            Order Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
	    
	    <!-- External Theme JS -->
	    <script src="<?php echo $BASE_URL; ?>assets/js/theme.js"></script>
    <main>


