<?php
/**
 * Register Custom Post Type and Meta Boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cts_register_testimonial_cpt() {
	$labels = array(
		'name'               => _x( 'Testimonials', 'post type general name', 'custom-testimonial-slider' ),
		'singular_name'      => _x( 'Testimonial', 'post type singular name', 'custom-testimonial-slider' ),
		'menu_name'          => _x( 'Testimonials', 'admin menu', 'custom-testimonial-slider' ),
		'add_new'            => _x( 'Add New', 'testimonial', 'custom-testimonial-slider' ),
		'add_new_item'       => __( 'Add New Testimonial', 'custom-testimonial-slider' ),
		'new_item'           => __( 'New Testimonial', 'custom-testimonial-slider' ),
		'edit_item'          => __( 'Edit Testimonial', 'custom-testimonial-slider' ),
		'view_item'          => __( 'View Testimonial', 'custom-testimonial-slider' ),
		'all_items'          => __( 'All Testimonials', 'custom-testimonial-slider' ),
		'search_items'       => __( 'Search Testimonials', 'custom-testimonial-slider' ),
		'not_found'          => __( 'No testimonials found.', 'custom-testimonial-slider' ),
		'not_found_in_trash' => __( 'No testimonials found in Trash.', 'custom-testimonial-slider' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'has_archive'        => false,
		'supports'           => array( 'title', 'editor', 'thumbnail' ),
		'menu_icon'          => 'dashicons-format-quote',
		'show_in_rest'       => true,
	);

	register_post_type( 'testimonial', $args );
}
add_action( 'init', 'cts_register_testimonial_cpt' );

/**
 * Add Meta Boxes
 */
function cts_add_testimonial_meta_boxes() {
	add_meta_box(
		'cts_testimonial_details',
		__( 'Testimonial Details', 'custom-testimonial-slider' ),
		'cts_testimonial_details_callback',
		'testimonial',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'cts_add_testimonial_meta_boxes' );

function cts_testimonial_details_callback( $post ) {
	wp_nonce_field( 'cts_save_testimonial_details', 'cts_testimonial_nonce' );

	$role = get_post_meta( $post->ID, '_cts_role', true );
	$rating = get_post_meta( $post->ID, '_cts_rating', true );
	$tag = get_post_meta( $post->ID, '_cts_tag', true );

	echo '<p><label for="_cts_role">' . __( 'Role/Job Title', 'custom-testimonial-slider' ) . '</label><br>';
	echo '<input type="text" id="_cts_role" name="_cts_role" value="' . esc_attr( $role ) . '" class="widefat"></p>';

	echo '<p><label for="_cts_rating">' . __( 'Rating (1-5)', 'custom-testimonial-slider' ) . '</label><br>';
	echo '<input type="number" id="_cts_rating" name="_cts_rating" value="' . esc_attr( $rating ) . '" min="1" max="5" class="small-text"></p>';

	echo '<p><label for="_cts_tag">' . __( 'Tag (e.g. Verified customer)', 'custom-testimonial-slider' ) . '</label><br>';
	echo '<input type="text" id="_cts_tag" name="_cts_tag" value="' . esc_attr( $tag ) . '" class="widefat"></p>';

	echo '<h4>' . __( '2x2 Grid Images', 'custom-testimonial-slider' ) . '</h4>';
	for ( $i = 1; $i <= 4; $i++ ) {
		$img_id = get_post_meta( $post->ID, '_cts_grid_img_' . $i, true );
		$img_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
		echo '<div class="cts-meta-image-field" style="margin-bottom: 10px;">';
		echo '<label>' . sprintf( __( 'Image %d', 'custom-testimonial-slider' ), $i ) . '</label><br>';
		echo '<div class="cts-preview-wrapper" style="margin-bottom: 5px;">' . ( $img_url ? '<img src="' . esc_url( $img_url ) . '" style="max-width:100px;height:auto;">' : '' ) . '</div>';
		echo '<input type="hidden" name="_cts_grid_img_' . $i . '" id="_cts_grid_img_' . $i . '" value="' . esc_attr( $img_id ) . '">';
		echo '<button type="button" class="button cts-upload-button" data-field="_cts_grid_img_' . $i . '">' . __( 'Select Image', 'custom-testimonial-slider' ) . '</button> ';
		echo '<button type="button" class="button cts-remove-button" data-field="_cts_grid_img_' . $i . '">' . __( 'Remove', 'custom-testimonial-slider' ) . '</button>';
		echo '</div>';
	}
	?>
	<script>
	jQuery(document).ready(function($){
		$('.cts-upload-button').on('click', function(e) {
			e.preventDefault();
			var button = $(this);
			var fieldId = button.data('field');

			var frame = wp.media({
				title: 'Select Image',
				button: { text: 'Use this image' },
				multiple: false
			});

			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				$('#' + fieldId).val(attachment.id);
				var thumb = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
				button.siblings('.cts-preview-wrapper').html('<img src="' + thumb + '" style="max-width:100px;height:auto;">');
			});

			frame.open();
		});

		$('.cts-remove-button').on('click', function(e) {
			e.preventDefault();
			var fieldId = $(this).data('field');
			$('#' + fieldId).val('');
			$(this).siblings('.cts-preview-wrapper').html('');
		});
	});
	</script>
	<?php
}

function cts_save_testimonial_details( $post_id ) {
	if ( ! isset( $_POST['cts_testimonial_nonce'] ) || ! wp_verify_nonce( $_POST['cts_testimonial_nonce'], 'cts_save_testimonial_details' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = [ '_cts_role', '_cts_rating', '_cts_tag', '_cts_grid_img_1', '_cts_grid_img_2', '_cts_grid_img_3', '_cts_grid_img_4' ];
	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $field ] ) );
		}
	}
}
add_action( 'save_post', 'cts_save_testimonial_details' );

/**
 * Enqueue media uploader scripts in admin
 */
function cts_admin_scripts( $hook ) {
	global $post;
	if ( ( 'post.php' == $hook || 'post-new.php' == $hook ) && isset($post->post_type) && 'testimonial' == $post->post_type ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'cts_admin_scripts' );
