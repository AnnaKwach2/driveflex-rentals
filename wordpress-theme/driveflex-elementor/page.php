<?php
get_header();
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$is_fleet = is_page( 'fleet' );
		$classes  = driveflex_elementor_page() || $is_fleet ? '' : 'df-content';
		?>
		<article <?php post_class( $classes ); ?>>
			<?php if ( ! driveflex_elementor_page() && ! $is_fleet ) : ?>
				<h1><?php the_title(); ?></h1>
				<?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) { rank_math_the_breadcrumbs(); } ?>
			<?php endif; ?>
			<?php the_content(); ?>
		</article>
		<?php
	endwhile;
endif;
get_footer();
