<?php 
include "connection.php"; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch restaurant settings
$settingsQuery = "SELECT * FROM settings LIMIT 1";
$settingsResult = $conn->query($settingsQuery);
$settings = $settingsResult ? $settingsResult->fetch_assoc() : null;

// Get current category
$current_cat = isset($_GET['category']) ? $_GET['category'] : '';

// If no category selected, get the first one
if (empty($current_cat)) {
    $firstCatQuery = "SELECT cat_name FROM categories ORDER BY `Order` ASC LIMIT 1";
    $firstCatResult = $conn->query($firstCatQuery);
    if ($firstCatResult && $firstCatResult->num_rows > 0) {
        $firstCat = $firstCatResult->fetch_assoc();
        $current_cat = $firstCat['cat_name'];
    }
}
?>

<?php include 'header.php' ?>

<!-- Custom Viewport for Menu Page (Zoomed Out) -->
<script>
    document.querySelector('meta[name="viewport"]').setAttribute("content", "width=device-width, initial-scale=0.8");
    // Pass restaurant settings to JS
    window.restaurantPhone = "<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>";
</script>

<!-- External CSS for Menu -->
<link rel="stylesheet" href="style/menu.css">

<section class="menu-header">
    <div class="menu-header-bg" style="background-image: url('<?php echo htmlspecialchars($settings['menu_bg'] ?? 'bgs/menu-bg.jpg'); ?>');"></div>
    <div class="menu-header-overlay"></div>
    <div class="container">
        <h1>Our Menu</h1>
        <p>Authentic flavors prepared with passion</p>
    </div>
</section>

<div class="category-tabs-wrapper">
    <div class="container">
        <div class="category-tabs">
            <?php
            $catSql = "SELECT * FROM categories ORDER BY `Order` ASC";
            $catResult = $conn->query($catSql);
            if ($catResult && $catResult->num_rows > 0) {
                while($cat = $catResult->fetch_assoc()) {
                    $isActive = ($current_cat == $cat['cat_name']) ? 'active' : '';
                    $iconHtml = !empty($cat['cat_icon']) ? '<img src="'.htmlspecialchars($cat['cat_icon']).'" class="category-icon" alt="">' : '';
                    echo '<a href="menu.php?category='.urlencode($cat['cat_name']).'" class="category-tab '.$isActive.'">'.$iconHtml.'<span>'.htmlspecialchars($cat['cat_name']).'</span></a>';
                }
            }
            ?>
        </div>
    </div>
</div>

<section class="menu-content">
    <div class="container">
        <div class="menu-grid">
            <?php
            if (!empty($current_cat)) {
                // Use prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT * FROM items WHERE item_category = ? ORDER BY `Order` ASC");
                $stmt->bind_param("s", $current_cat);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    while($item = $result->fetch_assoc()) {
                        ?>
                        <a href="ingredients.php?item=<?php echo urlencode($item['item_id']); ?>" style="text-decoration: none; color: inherit;">
                        <div class="menu-card" 
                             data-id="<?php echo $item['item_id']; ?>"
                             data-name="<?php echo htmlspecialchars($item['item_name']); ?>"
                             data-price-lbp="<?php echo $item['item_pricelbp']; ?>"
                             data-price-usd="<?php echo $item['item_priceusd']; ?>">
                            <?php if (!empty($item['item_pic'])): ?>
                                <div class="menu-card-img-container">
                                    <img src="<?php echo htmlspecialchars($item['item_pic']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" class="menu-card-img">
                                </div>
                            <?php else: ?>
                                <div class="menu-card-img" style="background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-utensils" style="font-size: 48px; color: #cbd5e1;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="menu-card-body">
                                <h3 class="menu-card-title"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                <div class="menu-card-price">
                                    <?php 
                                    $lbp_price = $item['item_pricelbp'] > 0 ? number_format($item['item_pricelbp'], 0, '.', ',') . ' LBP' : '';
                                    $usd_price = $item['item_priceusd'] > 0 ? '$' . number_format($item['item_priceusd'], 2) : '';
                                    ?>
                                    <span class="price-lbp" style="display: none;"><?php echo $lbp_price; ?></span>
                                    <span class="price-usd" style="display: none;"><?php echo $usd_price; ?></span>
                                </div>
                                <?php if (!empty($item['Ingredients']) && $item['Ingredients'] !== '0'): ?>
                                    <p class="menu-card-desc"><?php echo htmlspecialchars($item['Ingredients']); ?></p>
                                <?php endif; ?>
                                <div class="cart-controls" onclick="event.preventDefault();">
                                    <!-- Buttons injected by JS -->
                                </div>
                            </div>
                        </div>
                        </a>
                        <?php
                    }
                } else {
                    echo '<div class="col-12 no-items"><h3>No items found in this category.</h3></div>';
                }
                $stmt->close();
            } else {
                echo '<div class="col-12 no-items"><h3>Please select a category.</h3></div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Cart Modal -->
<div id="cart-modal">
    <div class="cart-modal-content">
        <div class="cart-header">
            <h2>Your Order</h2>
            <button class="close-cart" onclick="cart.closeCart()"><i class="fas fa-times"></i></button>
        </div>
        <div id="cart-items-container">
            <!-- Items injected by JS -->
        </div>
        <div id="cart-totals"></div>
        <button class="checkout-btn" onclick="cart.checkout()">
            <i class="fab fa-whatsapp"></i> Order on WhatsApp
        </button>
    </div>
</div>

<!-- External JS for Menu -->
<script src="JS/menu.js"></script>
<script src="JS/cart.js"></script>
<?php
$display_currency = $settings['display_currency'] ?? 'LBP';
?>
<script>
    // Currency display based on admin settings
    const displayCurrency = "<?php echo $display_currency; ?>";
    
    function updateMenuCurrency() {
        const priceLbpElements = document.querySelectorAll('.price-lbp');
        const priceUsdElements = document.querySelectorAll('.price-usd');
        
        priceLbpElements.forEach(el => {
            el.style.display = (displayCurrency === 'LBP' || displayCurrency === 'BOTH') ? 'block' : 'none';
        });
        
        priceUsdElements.forEach(el => {
            el.style.display = (displayCurrency === 'USD' || displayCurrency === 'BOTH') ? 'block' : 'none';
        });
    }
    
    // Run on page load
    document.addEventListener('DOMContentLoaded', updateMenuCurrency);
</script>

<?php include 'footer.php' ?>
