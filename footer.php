    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <a href="<?php echo home_url('/'); ?>" class="footer-logo">
                            <img src="<?php echo mjsk_asset('media/photos/logo-circular.png'); ?>" alt="Marbella JetSki Logo" style="height: 70px; margin-bottom: 15px;">
                            <span class="logo-text">MARBELLA<span class="logo-highlight">JETSKI</span></span>
                        </a>
                        <p>Costa del Sol's premier water sports destination. Family-owned since 1998, led by 4x Spanish National Champion Daniel Stiers.</p>
                        <div class="footer-certifications">
                            <img src="https://marbellajetski.com/wp-content/uploads/2022/05/WhatsApp-Image-2022-05-13-at-7.22.08-PM.jpeg" alt="ISO 9001 Certificate" loading="lazy">
                            <img src="https://marbellajetski.com/wp-content/uploads/2022/05/WhatsApp-Image-2022-05-13-at-7.22.21-PM.jpeg" alt="ISO 14001 Certificate" loading="lazy">
                        </div>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Quick Links</h4>
                        <?php if (has_nav_menu('footer-quick')) : ?>
                            <?php wp_nav_menu(['theme_location' => 'footer-quick', 'container' => false]); ?>
                        <?php else : ?>
                            <ul>
                                <li><a href="<?php echo home_url('/#services'); ?>">Services</a></li>
                                <li><a href="<?php echo home_url('/#jetski'); ?>">Jet Ski Hire</a></li>
                                <li><a href="<?php echo home_url('/#watersports'); ?>">Water Sports</a></li>
                                <li><a href="<?php echo home_url('/#boats'); ?>">Yacht Charters</a></li>
                                <li><a href="<?php echo home_url('/about-us/'); ?>">About Us</a></li>
                                <li><a href="<?php echo home_url('/#racing-lessons'); ?>">Racing Lessons</a></li>
                                <li><a href="<?php echo home_url('/booking/'); ?>">Book Now</a></li>
                            </ul>
                        <?php endif; ?>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Information</h4>
                        <?php if (has_nav_menu('footer-info')) : ?>
                            <?php wp_nav_menu(['theme_location' => 'footer-info', 'container' => false]); ?>
                        <?php else : ?>
                            <ul>
                                <li><a href="<?php echo home_url('/#faq'); ?>">FAQ</a></li>
                                <li><a href="<?php echo home_url('/booking/'); ?>">Book Online</a></li>
                                <li><a href="<?php echo home_url('/terms/#legal-notice'); ?>">Legal Notice</a></li>
                                <li><a href="<?php echo home_url('/terms/#terms'); ?>">Terms & Conditions</a></li>
                                <li><a href="<?php echo home_url('/terms/#privacy'); ?>">Privacy Policy</a></li>
                                <li><a href="<?php echo home_url('/terms/#cancellation'); ?>">Cancellation Policy</a></li>
                                <li><a href="<?php echo home_url('/weather-policy/'); ?>">Weather Policy</a></li>
                                <li><a href="<?php echo home_url('/terms/#cookies'); ?>">Cookie Policy</a></li>
                            </ul>
                        <?php endif; ?>
                    </div>
                    
                    <div class="footer-contact">
                        <h4>Contact Us</h4>
                        <div class="footer-contact-item">
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:<?php echo esc_attr(str_replace(' ', '', mjsk_get('mjsk_phone'))); ?>"><?php echo esc_html(mjsk_get('mjsk_phone')); ?></a>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fab fa-whatsapp"></i>
                            <a href="https://api.whatsapp.com/send?phone=<?php echo esc_attr(mjsk_get('mjsk_whatsapp')); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo esc_attr(mjsk_get('mjsk_email')); ?>"><?php echo esc_html(mjsk_get('mjsk_email')); ?></a>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo esc_html(mjsk_get('mjsk_address')); ?></span>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo esc_html(mjsk_get('mjsk_hours')); ?></span>
                        </div>
                        
                        <div class="footer-social">
                            <?php if ($url = mjsk_get('mjsk_facebook')) : ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <?php endif; ?>
                            <?php if ($url = mjsk_get('mjsk_instagram')) : ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                            <?php if ($url = mjsk_get('mjsk_tiktok')) : ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                            <?php endif; ?>
                            <?php if ($url = mjsk_get('mjsk_youtube')) : ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                            <?php endif; ?>
                            <?php if ($url = mjsk_get('mjsk_tripadvisor')) : ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="TripAdvisor"><i class="fab fa-tripadvisor"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; <?php echo date('Y'); ?> STIERS E HIJOS S.L. (Marbella JetSki). All rights reserved. NIF: B92917178</p>
                    <p class="credit">Designed with 💙 for Summer <?php echo date('Y'); ?></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/<?php echo esc_attr(mjsk_get('mjsk_whatsapp')); ?>?text=<?php echo rawurlencode("Hi! I'm interested in booking a water sports experience in Marbella. Can you help me?"); ?>" 
       class="whatsapp-float" 
       target="_blank" rel="noopener noreferrer" 
       aria-label="Chat on WhatsApp"
       id="whatsappFloat">
        <i class="fab fa-whatsapp"></i>
        <span class="whatsapp-tooltip">Chat with us!</span>
    </a>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <?php wp_footer(); ?>
</body>
</html>
