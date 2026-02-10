<?php
/**
 * Testimonial Slider Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cts_testimonial_slider_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'posts_per_page' => 5,
	), $atts );

	$query = new WP_Query( array(
		'post_type'      => 'testimonial',
		'posts_per_page' => $atts['posts_per_page'],
	) );

	if ( ! $query->have_posts() ) {
		return '';
	}

	// Enqueue registered assets
	wp_enqueue_style( 'cts-style' );
	wp_enqueue_script( 'cts-script' );

	ob_start();
	?>
	<div class="cts-slider-container">
		<div class="swiper cts-swiper">
			<div class="swiper-wrapper">
				<?php while ( $query->have_posts() ) : $query->the_post();
					$role = get_post_meta( get_the_ID(), '_cts_role', true );
					$rating = (int) get_post_meta( get_the_ID(), '_cts_rating', true );
					$tag = get_post_meta( get_the_ID(), '_cts_tag', true );
					$avatar_url = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ?: 'https://via.placeholder.com/60';

					$stars_html = str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating );

					$grid_html = '';
					for ( $i = 1; $i <= 4; $i++ ) {
						$img_id = get_post_meta( get_the_ID(), '_cts_grid_img_' . $i, true );
						$img_url = wp_get_attachment_image_url( $img_id, 'medium' ) ?: 'https://via.placeholder.com/300';
						$grid_html .= '<div class="cts-grid-item"><img src="' . esc_url( $img_url ) . '" alt=""></div>';
					}
				?>
					<div class="swiper-slide">
						<div class="cts-testimonial-slide">
							<div class="cts-testimonial-card">
								<div class="cts-card-header">
									<div class="cts-stars"><?php echo esc_html( $stars_html ); ?></div>
									<div class="cts-quote-icon">❝</div>
								</div>

								<div class="cts-author-info">
									<div class="cts-avatar-wrapper">
										<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php the_title_attribute(); ?>" class="cts-avatar">
										<div class="cts-verified-badge">✓</div>
									</div>
									<div class="cts-author-details">
										<h4><?php the_title(); ?></h4>
										<span><?php echo esc_html( $role ); ?></span>
									</div>
								</div>

								<div class="cts-testimonial-content">
									<?php the_content(); ?>
								</div>

								<?php if ( $tag ) : ?>
									<div class="cts-testimonial-tag"><?php echo esc_html( $tag ); ?></div>
								<?php endif; ?>
							</div>

							<div class="cts-image-grid">
								<?php echo $grid_html; ?>
							</div>
						</div>
					</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>

		<div class="cts-nav-container">
			<div class="cts-prev">←</div>
			<div class="cts-next">→</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'testimonial_slider', 'cts_testimonial_slider_shortcode' );
