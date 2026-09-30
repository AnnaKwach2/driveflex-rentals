</main>
<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) : ?>
<footer class="df-footer" id="contact">
	<div class="df-container">
		<div class="df-footer-grid">
			<div><a class="df-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">Drive<span>Flex</span><small>RENTALS</small></a><p>Reliable car hire for city travel, airport transfers, business trips and adventures across Kenya.</p><div class="df-social" style="--df-social-size:<?php echo esc_attr( max( 18, min( 48, absint( get_theme_mod( 'driveflex_social_size', 27 ) ) ) ) ); ?>px" aria-label="Social media"><?php foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'x' => 'X', 'whatsapp' => 'WhatsApp' ) as $network => $label ) : ?><a href="<?php echo esc_url( get_theme_mod( 'driveflex_' . $network, 'whatsapp' === $network ? 'https://wa.me/254706449960' : 'https://' . $network . '.com/' ) ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" target="_blank" rel="noopener noreferrer"><?php echo driveflex_social_icon( $network ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><?php endforeach; ?></div></div>
			<div><h3>Explore</h3><?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'fallback_cb' => 'driveflex_default_menu' ) ); ?></div>
			<div><h3>Contact</h3><p><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact us</a></p><p><a href="tel:+254706449960"><?php echo esc_html( driveflex_option( 'driveflex_phone', '+254 706 449960' ) ); ?></a></p><p><a href="https://wa.me/254706449960" target="_blank" rel="noopener">Message us on WhatsApp</a></p><p><?php echo esc_html( driveflex_option( 'driveflex_email', 'bookings@example.com' ) ); ?></p><p><?php echo esc_html( driveflex_option( 'driveflex_location', 'Nairobi, Kenya' ) ); ?></p></div>
			<div><h3>Fleet</h3><ul><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">Luxury SUV</a></li><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">SUV</a></li><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">Sedan</a></li><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">Van &amp; Mini Van</a></li><li><a href="<?php echo esc_url( home_url( '/fleet/' ) ); ?>">Bus &amp; Pickup</a></li></ul></div>
		</div>
		<p class="df-footer-description">DriveFlex Rentals provides dependable self-drive, chauffeur-driven and airport car hire services across Kenya.</p>
		<div class="df-footer-bottom"><span>COPYRIGHT © <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( strtoupper( get_bloginfo( 'name' ) ) ); ?>. DESIGN BY <a href="https://www.designphox.com" target="_blank" rel="noopener">DesignPhox.</a></span><span><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Privacy Policy</a> &nbsp; | &nbsp; <a href="<?php echo esc_url( home_url( '/rental-terms/' ) ); ?>">Rental Terms</a></span></div>
	</div>
</footer>
<?php endif; ?>
<?php wp_footer(); ?>
</body></html>
