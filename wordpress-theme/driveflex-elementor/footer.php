</main>
<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) : ?>
<footer class="df-footer" id="contact">
	<div class="df-container">
		<div class="df-footer-grid">
			<div><a class="df-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">Drive<span>Flex</span><small>RENTALS</small></a><p>Reliable car hire for city travel, airport transfers, business trips and adventures across Kenya.</p><div class="df-social" aria-label="Social media"><a href="<?php echo esc_url( get_theme_mod( 'driveflex_facebook', 'https://facebook.com/' ) ); ?>" aria-label="Facebook">f</a><a href="<?php echo esc_url( get_theme_mod( 'driveflex_instagram', 'https://instagram.com/' ) ); ?>" aria-label="Instagram">◎</a><a href="<?php echo esc_url( get_theme_mod( 'driveflex_linkedin', 'https://linkedin.com/' ) ); ?>" aria-label="LinkedIn">in</a><a href="<?php echo esc_url( get_theme_mod( 'driveflex_x', 'https://x.com/' ) ); ?>" aria-label="X">𝕏</a></div></div>
			<div><h3>Explore</h3><?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'fallback_cb' => 'driveflex_default_menu' ) ); ?></div>
			<div><h3>Contact</h3><p><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact us</a></p><p><a href="tel:+254706449960"><?php echo esc_html( driveflex_option( 'driveflex_phone', '+254 706 449960' ) ); ?></a></p><p><a href="https://wa.me/254706449960" target="_blank" rel="noopener">Message us on WhatsApp</a></p><p><?php echo esc_html( driveflex_option( 'driveflex_email', 'bookings@example.com' ) ); ?></p><p><?php echo esc_html( driveflex_option( 'driveflex_location', 'Nairobi, Kenya' ) ); ?></p></div>
			<div><h3>Fleet</h3><ul><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">Luxury SUV</a></li><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">SUV</a></li><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">Sedan</a></li><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">Van &amp; Mini Van</a></li><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">Bus &amp; Pickup</a></li></ul></div>
		</div>
		<div class="df-footer-bottom"><span>COPYRIGHT © <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( strtoupper( get_bloginfo( 'name' ) ) ); ?>. DESIGN BY <a href="https://www.designphox.com" target="_blank" rel="noopener">DesignPhox.</a></span><span><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Privacy Policy</a></span></div>
	</div>
</footer>
<?php endif; ?>
<?php wp_footer(); ?>
</body></html>
