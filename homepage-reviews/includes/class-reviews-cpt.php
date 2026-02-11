<?php
if (!defined('ABSPATH')) {
    exit;
}

class Homepage_Reviews_CPT
{

    public function __construct()
    {
        add_action('init', array($this, 'register_cpt'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box_data'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_filter('manage_homepage_reviews_posts_columns', array($this, 'add_sort_column'));
        add_action('manage_homepage_reviews_posts_custom_column', array($this, 'render_sort_column'), 10, 2);
        add_action('wp_ajax_update_reviews_order', array($this, 'handle_ajax_reorder'));
        add_action('pre_get_posts', array($this, 'set_admin_order'));
    }

    public function enqueue_admin_scripts()
    {
        global $post_type, $pagenow;
        if ('homepage_reviews' === $post_type) {
            wp_enqueue_media();
            wp_enqueue_script('homepage-reviews-admin', HOMEPAGE_REVIEWS_URL . 'assets/js/admin.js', array('jquery'), '1.0.0', true);

            if ('edit.php' === $pagenow) {
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script('homepage-reviews-reorder', HOMEPAGE_REVIEWS_URL . 'assets/js/admin-reorder.js', array('jquery', 'jquery-ui-sortable'), '1.0.0', true);
                wp_localize_script('homepage-reviews-reorder', 'homepage_reviews_reorder', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('homepage_reviews_reorder_nonce')
                ));

                wp_register_style('homepage-reviews-admin-reorder', false, array(), '1.0.0');
                wp_enqueue_style('homepage-reviews-admin-reorder');
                wp_add_inline_style('homepage-reviews-admin-reorder', '.column-sort { width: 40px !important; text-align: center; } .drag-handle { cursor: move; color: #ccc; font-size: 20px; } .drag-handle:hover { color: #666; } .ui-sortable-helper { display: table !important; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }');
            }
        }
    }

    public function add_sort_column($columns)
    {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $new_columns['sort'] = '';
            }
            $new_columns[$key] = $value;
        }
        return $new_columns;
    }

    public function render_sort_column($column, $post_id)
    {
        if ($column === 'sort') {
            echo '<span class="dashicons dashicons-menu drag-handle"></span>';
        }
    }

    public function handle_ajax_reorder()
    {
        check_ajax_referer('homepage_reviews_reorder_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
        }

        if (isset($_POST['order'])) {
            $order = array_map('intval', wp_unslash($_POST['order']));
        } else {
            $order = array();
        }

        if (empty($order)) {
            wp_send_json_error('No order data');
        }

        foreach ($order as $index => $post_id) {
            $post_id = intval($post_id);
            if ($post_id > 0) {
                wp_update_post(array(
                    'ID' => $post_id,
                    'menu_order' => $index
                ));
            }
        }

        wp_send_json_success();
    }

    public function set_admin_order($query)
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') === 'homepage_reviews') {
            $query->set('orderby', 'menu_order');
            $query->set('order', 'ASC');
        }
    }

    public function register_cpt()
    {
        $labels = array(
            'name' => _x('Reviews', 'Post Type General Name', 'homepage-reviews'),
            'singular_name' => _x('Review', 'Post Type Singular Name', 'homepage-reviews'),
            'menu_name' => __('Homepage Reviews', 'homepage-reviews'),
            'name_admin_bar' => __('Review', 'homepage-reviews'),
            'archives' => __('Review Archives', 'homepage-reviews'),
            'attributes' => __('Review Attributes', 'homepage-reviews'),
            'parent_item_colon' => __('Parent Review:', 'homepage-reviews'),
            'all_items' => __('All Reviews', 'homepage-reviews'),
            'add_new_item' => __('Add New Review', 'homepage-reviews'),
            'add_new' => __('Add New', 'homepage-reviews'),
            'new_item' => __('New Review', 'homepage-reviews'),
            'edit_item' => __('Edit Review', 'homepage-reviews'),
            'update_item' => __('Update Review', 'homepage-reviews'),
            'view_item' => __('View Review', 'homepage-reviews'),
            'view_items' => __('View Reviews', 'homepage-reviews'),
            'search_items' => __('Search Review', 'homepage-reviews'),
            'not_found' => __('Not found', 'homepage-reviews'),
            'not_found_in_trash' => __('Not found in Trash', 'homepage-reviews'),
            'featured_image' => __('Reviewer Image', 'homepage-reviews'),
            'set_featured_image' => __('Set reviewer image', 'homepage-reviews'),
            'remove_featured_image' => __('Remove reviewer image', 'homepage-reviews'),
            'use_featured_image' => __('Use as reviewer image', 'homepage-reviews'),
            'insert_into_item' => __('Insert into review', 'homepage-reviews'),
            'uploaded_to_this_item' => __('Uploaded to this review', 'homepage-reviews'),
            'items_list' => __('Reviews list', 'homepage-reviews'),
            'items_list_navigation' => __('Reviews list navigation', 'homepage-reviews'),
            'filter_items_list' => __('Filter reviews list', 'homepage-reviews'),
        );
        $args = array(
            'label' => __('Review', 'homepage-reviews'),
            'description' => __('Homepage Reviews', 'homepage-reviews'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-format-quote',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'rewrite' => false,
            'capability_type' => 'post',
        );
        register_post_type('homepage_reviews', $args);
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'review_details',
            __('Review Details', 'homepage-reviews'),
            array($this, 'render_meta_box'),
            'homepage_reviews',
            'normal',
            'high'
        );

        add_meta_box(
            'review_gallery',
            __('Review Gallery (4 Images)', 'homepage-reviews'),
            array($this, 'render_gallery_meta_box'),
            'homepage_reviews',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post)
    {
        wp_nonce_field('homepage_reviews_save_meta_box_data', 'homepage_reviews_meta_box_nonce');

        $rating = get_post_meta($post->ID, '_review_rating', true);
        $position = get_post_meta($post->ID, '_reviewer_position', true);
        $tag = get_post_meta($post->ID, '_review_tag', true);

        echo '<p>';
        echo '<label for="review_rating">' . esc_html__('Rating (1-5)', 'homepage-reviews') . '</label>';
        echo '<input type="number" id="review_rating" name="review_rating" value="' . esc_attr($rating) . '" min="1" max="5" step="0.5" style="width: 100%;" />';
        echo '</p>';

        echo '<p>';
        echo '<label for="reviewer_position">' . esc_html__('Reviewer Position/Title', 'homepage-reviews') . '</label>';
        echo '<input type="text" id="reviewer_position" name="reviewer_position" value="' . esc_attr($position) . '" style="width: 100%;" />';
        echo '</p>';

        echo '<p>';
        echo '<label for="review_tag">' . esc_html__('Review Tag (e.g., Living Room Makeover)', 'homepage-reviews') . '</label>';
        echo '<input type="text" id="review_tag" name="review_tag" value="' . esc_attr($tag) . '" style="width: 100%;" />';
        echo '</p>';

        $verified = get_post_meta($post->ID, '_review_verified', true);
        $is_verified = ($verified !== 'no'); // Default to checked

        echo '<p>';
        echo '<label for="review_verified">';
        echo '<input type="checkbox" id="review_verified" name="review_verified" value="yes" ' . checked($is_verified, true, false) . ' />';
        echo esc_html__('Show Verified Badge', 'homepage-reviews');
        echo '</label>';
        echo '</p>';
    }

    public function render_gallery_meta_box($post)
    {
        for ($i = 1; $i <= 4; $i++) {
            $image_id = get_post_meta($post->ID, '_review_gallery_image_' . $i, true);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';

            echo '<div class="gallery-image-wrapper" style="display: inline-block; margin-right: 10px; text-align: center;">';
            echo '<div class="image-preview-' . esc_attr($i) . '" style="width: 100px; height: 100px; background: #f0f0f0; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; margin-bottom: 5px;">';
            if ($image_url) {
                echo '<img src="' . esc_url($image_url) . '" style="max-width: 100%; max-height: 100%;" />';
            } else {
                echo '<span>' . esc_html__('No Image', 'homepage-reviews') . '</span>';
            }
            echo '</div>';
            echo '<input type="hidden" name="review_gallery_image_' . esc_attr($i) . '" id="review_gallery_image_' . esc_attr($i) . '" value="' . esc_attr($image_id) . '" />';
            echo '<button type="button" class="button upload-gallery-image" data-index="' . esc_attr($i) . '">' . esc_html__('Select Image', 'homepage-reviews') . '</button>';
            echo '<button type="button" class="button remove-gallery-image" data-index="' . esc_attr($i) . '" style="margin-top: 5px;">' . esc_html__('Remove', 'homepage-reviews') . '</button>';
            echo '</div>';
        }
    }

    public function save_meta_box_data($post_id)
    {
        if (!isset($_POST['homepage_reviews_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce(sanitize_key($_POST['homepage_reviews_meta_box_nonce']), 'homepage_reviews_save_meta_box_data')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['review_rating'])) {
            update_post_meta($post_id, '_review_rating', sanitize_text_field(wp_unslash($_POST['review_rating'])));
        }

        if (isset($_POST['reviewer_position'])) {
            update_post_meta($post_id, '_reviewer_position', sanitize_text_field(wp_unslash($_POST['reviewer_position'])));
        }

        if (isset($_POST['review_tag'])) {
            update_post_meta($post_id, '_review_tag', sanitize_text_field(wp_unslash($_POST['review_tag'])));
        }

        $verified = isset($_POST['review_verified']) ? 'yes' : 'no';
        update_post_meta($post_id, '_review_verified', $verified);

        for ($i = 1; $i <= 4; $i++) {
            if (isset($_POST['review_gallery_image_' . $i])) {
                update_post_meta($post_id, '_review_gallery_image_' . $i, absint($_POST['review_gallery_image_' . $i]));
            }
        }
    }
}
