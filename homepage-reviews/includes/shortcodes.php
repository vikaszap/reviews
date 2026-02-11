<?php
if (!defined('ABSPATH')) {
    exit;
}

function homepage_reviews_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'limit' => -1,
    ), $atts, 'homepage_reviews');

    // Enqueue styles and scripts
    wp_enqueue_style('homepage-reviews-style', HOMEPAGE_REVIEWS_URL . 'assets/css/style.css', array(), '1.0.0');
    wp_enqueue_script('homepage-reviews-slider', HOMEPAGE_REVIEWS_URL . 'assets/js/slider.js', array('jquery'), '1.0.0', true);

    // Get settings
    $options = get_option('homepage_reviews_settings');
    $card_bg_color = isset($options['card_bg_color']) ? $options['card_bg_color'] : '#ffffff';
    $text_color = isset($options['text_color']) ? $options['text_color'] : '#333333';
    $rating_color = isset($options['rating_color']) ? $options['rating_color'] : '#ffb900';
    $font_size = isset($options['font_size']) ? $options['font_size'] : '16';
    $border_radius = isset($options['border_radius']) ? $options['border_radius'] : '8';

    // Query Reviews
    $args = array(
        'post_type' => 'homepage_reviews',
        'posts_per_page' => $atts['limit'],
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );
    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>' . __('No reviews found.', 'homepage-reviews') . '</p>';
    }

    // Generate Custom Styles
    $custom_css = "
        .homepage-reviews-slider .review-card {
            background-color: {$card_bg_color};
            color: {$text_color};
            font-size: {$font_size}px;
            border-radius: {$border_radius}px;
        }
        .homepage-reviews-slider .review-rating {
            color: {$rating_color};
        }
    ";
    wp_add_inline_style('homepage-reviews-style', $custom_css);

    // Build Output
    $output = '<div class="homepage-reviews-slider">';
    $output .= '<div class="reviews-track">';

    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $rating = get_post_meta($post_id, '_review_rating', true);
        $position = get_post_meta($post_id, '_reviewer_position', true);
        $tag = get_post_meta($post_id, '_review_tag', true);
        $is_verified = get_post_meta($post_id, '_review_verified', true);
        $thumbnail = get_the_post_thumbnail_url($post_id, 'thumbnail');

        // Collect gallery images first to determine layout
        $gallery_images = array();
        for ($gi = 1; $gi <= 4; $gi++) {
            $image_id = get_post_meta($post_id, '_review_gallery_image_' . $gi, true);
            if ($image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'large');
                if ($image_url) {
                    $gallery_images[] = $image_url;
                }
            }
        }
        $gallery_count = count($gallery_images);

        // Slide wrapper — add no-gallery-slide class when no images
        $slide_class = 'review-slide';
        if ($gallery_count === 0) {
            $slide_class .= ' no-gallery-slide';
        }
        $output .= '<div class="' . $slide_class . '">';

        // Left Column: Testimonial Card
        $output .= '<div class="review-card">';

        // Header: Image + Info + Quote Icon
        $output .= '<div class="review-card-header">';

        $output .= '<div class="reviewer-info-wrapper">';
        if ($thumbnail) {
            $output .= '<div class="reviewer-image-wrapper">';
            $output .= '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr(get_the_title()) . '" class="reviewer-image">';
            // Verified Badge (SVG)
            if ($is_verified !== 'no') {
                $output .= '<span class="verified-badge"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M0 11.9915C0 5.36875 5.36875 0 11.9915 0C18.6141 0 23.9829 5.36875 23.9829 11.9915C23.9829 18.6141 18.6141 23.9829 11.9915 23.9829C5.36875 23.9829 0 18.6141 0 11.9915Z" fill="#155DFC"/>
						<path d="M11.6749 5.52464C11.7041 5.46564 11.7492 5.41597 11.8052 5.38125C11.8611 5.34653 11.9256 5.32812 11.9915 5.32812C12.0573 5.32812 12.1218 5.34653 12.1777 5.38125C12.2337 5.41597 12.2788 5.46564 12.308 5.52464L13.8474 8.64271C13.9488 8.84794 14.0985 9.02549 14.2836 9.16014C14.4687 9.29478 14.6838 9.38249 14.9103 9.41573L18.3529 9.91953C18.4181 9.92898 18.4794 9.95649 18.5298 9.99896C18.5802 10.0414 18.6177 10.0972 18.6381 10.1598C18.6585 10.2225 18.6609 10.2896 18.6452 10.3536C18.6294 10.4176 18.596 10.4759 18.5488 10.5219L16.0591 12.9463C15.895 13.1063 15.7721 13.3038 15.7012 13.5218C15.6302 13.7398 15.6134 13.9718 15.652 14.1978L16.2397 17.6231C16.2513 17.6883 16.2442 17.7554 16.2194 17.8168C16.1946 17.8782 16.1531 17.9313 16.0995 17.9703C16.0459 18.0092 15.9825 18.0322 15.9164 18.0368C15.8504 18.0414 15.7844 18.0274 15.7259 17.9963L12.6485 16.3782C12.4458 16.2718 12.2202 16.2161 11.9911 16.2161C11.7621 16.2161 11.5365 16.2718 11.3337 16.3782L8.25697 17.9963C8.19855 18.0272 8.13262 18.0411 8.06668 18.0364C8.00075 18.0317 7.93745 18.0086 7.88399 17.9697C7.83053 17.9309 7.78905 17.8778 7.76428 17.8165C7.7395 17.7552 7.73242 17.6882 7.74385 17.6231L8.33094 14.1985C8.36972 13.9724 8.35292 13.7402 8.28199 13.5221C8.21105 13.3039 8.08811 13.1063 7.92377 12.9463L5.43412 10.5226C5.38653 10.4767 5.35281 10.4183 5.3368 10.3541C5.32078 10.2899 5.32312 10.2225 5.34354 10.1596C5.36396 10.0966 5.40164 10.0407 5.45229 9.99815C5.50295 9.95559 5.56453 9.92812 5.63004 9.91886L9.07197 9.41573C9.29872 9.38274 9.51405 9.29515 9.69944 9.16049C9.88483 9.02583 10.0347 8.84814 10.1362 8.64271L11.6749 5.52464Z" fill="white" stroke="white" stroke-width="1.33279" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						</span>';
            }
            $output .= '</div>';
        }

        $output .= '<div class="reviewer-text">';
        $output .= '<div class="reviewer-name">' . get_the_title() . '</div>';
        if ($position) {
            $output .= '<div class="reviewer-position">' . esc_html($position) . '</div>';
        }
        $output .= '</div>'; // .reviewer-text
        $output .= '</div>'; // .reviewer-info-wrapper

        // Quote Icon (SVG)
        $output .= '<div class="quote-icon">';
        $output .= '<svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M42.6592 7.99805C41.2449 7.99805 39.8886 8.55985 38.8886 9.55987C37.8886 10.5599 37.3268 11.9162 37.3268 13.3304V29.3276C37.3268 30.7419 37.8886 32.0982 38.8886 33.0982C39.8886 34.0982 41.2449 34.66 42.6592 34.66C43.3663 34.66 44.0445 34.9409 44.5445 35.441C45.0445 35.941 45.3254 36.6191 45.3254 37.3262V39.9924C45.3254 41.4067 44.7636 42.763 43.7636 43.763C42.7635 44.763 41.4072 45.3248 39.993 45.3248C39.2859 45.3248 38.6077 45.6057 38.1077 46.1058C37.6077 46.6058 37.3268 47.2839 37.3268 47.991V53.3234C37.3268 54.0306 37.6077 54.7087 38.1077 55.2087C38.6077 55.7087 39.2859 55.9896 39.993 55.9896C44.2357 55.9896 48.3047 54.3042 51.3047 51.3042C54.3048 48.3041 55.9902 44.2352 55.9902 39.9924V13.3304C55.9902 11.9162 55.4284 10.5599 54.4284 9.55987C53.4283 8.55985 52.072 7.99805 50.6578 7.99805H42.6592Z" stroke="#DBEAFE" stroke-width="5.3324" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M13.331 7.99805C11.9168 7.99805 10.5604 8.55985 9.56042 9.55987C8.5604 10.5599 7.9986 11.9162 7.9986 13.3304V29.3276C7.9986 30.7419 8.5604 32.0982 9.56042 33.0982C10.5604 34.0982 11.9168 34.66 13.331 34.66C14.0381 34.66 14.7163 34.9409 15.2163 35.441C15.7163 35.941 15.9972 36.6191 15.9972 37.3262V39.9924C15.9972 41.4067 15.4354 42.763 14.4354 43.763C13.4354 44.763 12.079 45.3248 10.6648 45.3248C9.95768 45.3248 9.27952 45.6057 8.77951 46.1058C8.2795 46.6058 7.9986 47.2839 7.9986 47.991V53.3234C7.9986 54.0306 8.2795 54.7087 8.77951 55.2087C9.27952 55.7087 9.95768 55.9896 10.6648 55.9896C14.9075 55.9896 18.9765 54.3042 21.9765 51.3042C24.9766 48.3041 26.662 44.2352 26.662 39.9924V13.3304C26.662 11.9162 26.1002 10.5599 25.1002 9.55987C24.1002 8.55985 22.7438 7.99805 21.3296 7.99805H13.331Z" stroke="#DBEAFE" stroke-width="5.3324" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>';
        $output .= '</div>';

        $output .= '</div>'; // .review-card-header

        // Rating
        $output .= '<div class="review-rating">';
        for ($i = 1; $i <= 5; $i++) {
            $output .= ($i <= $rating) ? '&#9733;' : '&#9734;';
        }
        $output .= '</div>';

        // Content (with quotation marks like Figma)
        $output .= '<div class="review-content">&ldquo;' . get_the_content() . '&rdquo;</div>';

        // Tag
        if ($tag) {
            $output .= '<div class="review-tag">' . esc_html($tag) . '</div>';
        }

        $output .= '</div>'; // .review-card

        // Right Column: Gallery Grid (only if images exist)
        if ($gallery_count > 0) {
            $output .= '<div class="review-gallery gallery-count-' . $gallery_count . '" data-gallery-count="' . $gallery_count . '">';
            foreach ($gallery_images as $img_url) {
                $output .= '<div class="gallery-item"><img src="' . esc_url($img_url) . '" alt="' . esc_attr(get_the_title()) . '" loading="lazy"></div>';
            }
            $output .= '</div>'; // .review-gallery
        }

        $output .= '</div>'; // .review-slide
    }

    $output .= '</div>'; // .reviews-track

    // Navigation: Prev + Dots + Next
    $output .= '<div class="slider-nav">';
    $output .= '<button class="slider-prev"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>';
    $output .= '<div class="slider-dots">';
    $slide_count = $query->post_count;
    for ($d = 0; $d < $slide_count; $d++) {
        $active_class = ($d === 0) ? ' active' : '';
        $output .= '<span class="slider-dot' . $active_class . '" data-index="' . $d . '"></span>';
    }
    $output .= '</div>';
    $output .= '<button class="slider-next"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>';
    $output .= '</div>'; // .slider-nav

    $output .= '</div>'; // .homepage-reviews-slider

    wp_reset_postdata();

    return $output;
}
add_shortcode('homepage_reviews', 'homepage_reviews_shortcode');
