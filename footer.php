</main>
<!-- Custom Viewport for Menu Page (Zoomed Out) -->
<script>
    document.querySelector('meta[name="viewport"]').setAttribute("content", "width=device-width, initial-scale=0.89");
</script>
    <!-- External CSS for Footer -->
    <link rel="stylesheet" href="style/footer.css">

    <footer class="site-footer">
        <div class="container">
            <div class="footer-top">
                <div class="row gy-5">
                    <!-- Brand Column -->
                    <div class="col-lg-4">
                        <div class="footer-widget">
                            <h2 class="footer-brand"><?php echo htmlspecialchars($settings['restaurant_name'] ?? 'Restaurant'); ?></h2>
                            <p class="footer-desc"><?php echo htmlspecialchars($settings['restaurant_description'] ?? 'Authentic flavors prepared with passion.'); ?></p>
                            <div class="footer-social">
                                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? ''); ?>" class="social-link"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Hours Column -->
                    <div class="col-lg-3 col-md-4">
                        <div class="footer-widget">
                            <h4 class="widget-title"><?php echo htmlspecialchars($settings['opening_title'] ?? 'Opening Hours'); ?></h4>
                            <div class="hours-list">
                                <div class="hour-item">
                                    <span class="day">Monday - Sunday</span>
                                    <span class="time"><?php echo htmlspecialchars($settings['opening_hours'] ?? '12:00 PM - 11:00 PM'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Column -->
                    <div class="col-lg-3 col-md-4">
                        <div class="footer-widget">
                            <h4 class="widget-title">Contact Us</h4>
                            <ul class="contact-list">
                                <li>
                                    <i class="fas fa-phone-alt"></i>
                                    <a href="tel:<?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?>"><?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?></a>
                                </li>
                                <li>
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:<?php echo htmlspecialchars($settings['restaurant_email'] ?? 'info@restaurant.com'); ?>"><?php echo htmlspecialchars($settings['restaurant_email'] ?? 'info@restaurant.com'); ?></a>
                                </li>
                                <li>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($settings['restaurant_address'] ?? 'Lebanon'); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Links Column -->
                    <div class="col-lg-2 col-md-4">
                        <div class="footer-widget">
                            <h4 class="widget-title">Navigation</h4>
                            <ul class="footer-menu">
                                <li><a href="index.php">Home</a></li>
                                <li><a href="menu.php">Menu</a></li>
                                <li><a href="contact.php">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6">
                        <p class="copyright">&copy; 2026 Mustafa. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="developer">Designed by Mustafa Abou El-Hajj</i></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>