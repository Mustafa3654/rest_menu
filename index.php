<?php 
include "connection.php"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Fetch restaurant settings
$settingsQuery = "SELECT * FROM settings LIMIT 1";
$settingsResult = $conn->query($settingsQuery);
$settings = $settingsResult ? $settingsResult->fetch_assoc() : null;

$mapLink = $settings['restaurant_maps'] ?? '#';
?>

<?php include 'header.php' ?>

<!-- External CSS for Index -->
<link rel="stylesheet" href="style/index.css">

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-bg" style="background-image: url('<?php echo htmlspecialchars($settings['home_bg'] ?? 'bgs/hero-bg.jpg'); ?>');"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">Welcome to <span><?php echo htmlspecialchars($settings['restaurant_name'] ?? 'Our Kitchen'); ?></span></h1>
        <p class="hero-description">
            <?php echo htmlspecialchars($settings['restaurant_description'] ?? 'Experience the finest Lebanese flavors, crafted with tradition and served with love.'); ?>
        </p>
        <div class="hero-btns">
            <a href="menu.php" class="btn-hero-primary">View Our Menu</a>
        </div>
    </div>
</section>

<!-- Combined Info Section -->
<section class="combined-info-section">
    <div class="container">
        <div class="info-grid">
            <!-- Open Daily Box -->
            <div class="info-box reveal">
                <div class="info-box-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3><?php echo htmlspecialchars($settings['opening_title'] ?? 'Open Daily'); ?></h3>
                <p>We are ready to serve you every single day. Join us for an unforgettable dining experience.</p>
                <div class="highlight"><?php echo htmlspecialchars($settings['opening_hours'] ?? '12:00 PM - 11:00 PM'); ?></div>
            </div>

            <!-- Location Box -->
            <a href="<?php echo htmlspecialchars($mapLink); ?>" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: inherit; width: 100%;">
                <div class="info-box alt reveal">
                    <div class="info-box-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Our Location</h3>
                    <p>Find us in the heart of the city. We're easily accessible and waiting to welcome you.</p>
                    <div class="highlight">
                        <?php echo htmlspecialchars($settings['restaurant_address'] ?? '#'); ?>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
</section>

<!-- External JS for Index -->
<script src="JS/index.js"></script>

<?php include 'footer.php' ?>
