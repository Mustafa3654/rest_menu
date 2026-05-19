</main>

    <!-- External CSS for Footer -->
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>style/footer.css?v=<?php echo time(); ?>">

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-widget brand-widget">
                <span class="footer-pre-title">AUTHENTIC CUISINE</span>
                <h2 class="footer-title"><?php echo htmlspecialchars($settings['restaurant_name'] ?? 'Lebanese Kitchen'); ?></h2>
                <p class="footer-desc"><?php echo htmlspecialchars($settings['restaurant_description'] ?? 'A taste of Beirut, served with hospitality.'); ?></p>
            </div>

            <div class="footer-widget">
                <h4 class="widget-title">HOURS</h4>
                <div class="hours-list">
                    <span class="day-time"><?php echo htmlspecialchars($settings['opening_title'] ?? 'Opens Daily'); ?></span>
                    <span class="day-time"><?php echo htmlspecialchars($settings['opening_hours'] ?? '12:00a.m. - 12:00p.m.'); ?></span>
                </div>
            </div>

            <div class="footer-widget">
                <h4 class="widget-title">VISIT</h4>
                <div class="visit-list">
                    <span class="contact-item">Reservations: <a href="tel:<?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?>"><?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?></a></span>
                    <?php if (!empty($settings['restaurant_email'])): ?>
                    <span class="contact-item"><a href="mailto:<?php echo htmlspecialchars($settings['restaurant_email']); ?>"><?php echo htmlspecialchars($settings['restaurant_email']); ?></a></span>
                    <?php endif; ?>
                    <span class="contact-item"><?php echo htmlspecialchars($settings['restaurant_address'] ?? 'Lebanon'); ?></span>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">&copy; <?php echo date('Y'); ?> Mustafa Abou El-Hajj. All rights reserved.</p>
            </div>
        </div>
        
        <?php
        $mem_usage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        ?>
        <div style="
            position: fixed; 
            bottom: 10px; 
            right: 10px; 
            background: #222; 
            color: #00ff00; 
            padding: 8px 12px; 
            border-radius: 5px; 
            font-family: monospace; 
            font-size: 12px; 
            z-index: 99999;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        ">
            Memory: <?php echo $mem_usage; ?> MB
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

