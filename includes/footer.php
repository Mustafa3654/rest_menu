</main>

    <footer class="site-footer">
        <div class="max-w-6xl mx-auto px-4">
            <div class="footer-top">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6">
                    <!-- Brand Column -->
                    <div class="lg:col-span-4 md:col-span-full">
                        <div class="footer-widget">
                            <h2 class="footer-brand"><?php echo htmlspecialchars($settings['restaurant_name'] ?? 'Restaurant'); ?></h2>
                            <p class="footer-desc"><?php echo htmlspecialchars($settings['restaurant_description'] ?? 'Authentic flavors prepared with passion.'); ?></p>
                            <div class="footer-social lg:justify-start md:justify-center">
                                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', ($settings['country_code'] ?? '') . ($settings['whatsapp_number'] ?? '')); ?>" class="social-link"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Hours Column -->
                    <div class="lg:col-span-3 md:col-span-1">
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
                    <div class="lg:col-span-3 md:col-span-1">
                        <div class="footer-widget">
                            <h4 class="widget-title">Contact Us</h4>
                            <ul class="contact-list">
                                <li>
                                    <i class="fas fa-phone-alt"></i>
                                    <a href="tel:<?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?>"><?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?></a>
                                </li>

                                <?php if (!empty($settings['restaurant_email'])): ?>
                                <li>
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:<?php echo htmlspecialchars($settings['restaurant_email']); ?>"><?php echo htmlspecialchars($settings['restaurant_email']); ?></a>
                                </li>
                                <?php endif; ?>

                                <li>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($settings['restaurant_address'] ?? 'Lebanon'); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Links Column -->
                    <div class="lg:col-span-2 md:col-span-1">
                        <div class="footer-widget">
                            <h4 class="widget-title">Navigation Bar</h4>
                            <ul class="footer-menu">
                                <li><a href="<?php echo $BASE_URL; ?>index">Home</a></li>
                                <li><a href="<?php echo $BASE_URL; ?>menu">Menu</a></li>
                                <li><a href="<?php echo $BASE_URL; ?>contact">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <p class="copyright">&copy; 2026 Mustafa. All rights reserved.</p>
                    </div>
                    <div class="md:text-right">
                        <p class="developer">Designed by Mustafa Abou El-Hajj</p>
                    </div>
                </div>
            </div>
        </div>
        <?php
$mem_usage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
?>
<div class="debug-badge">
    Memory: <?php echo $mem_usage; ?> MB
</div>
    </footer>
</body>
</html>
