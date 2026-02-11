<?php
if (!defined('ABSPATH')) {
    exit;
}

class Homepage_Reviews_Demo
{

    public function __construct()
    {
        add_action('admin_post_homepage_reviews_import_demo_data', array($this, 'import_demo_data'));
    }

    public function import_demo_data()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        check_admin_referer('homepage_reviews_import_demo_data_action', 'homepage_reviews_import_demo_data_nonce');

        $demo_reviews = array(
            array(
                'title' => 'John Doe',
                'content' => 'This product is amazing! Highly recommended.',
                'rating' => 5,
                'position' => 'CEO, Example Inc.',
            ),
            array(
                'title' => 'Jane Smith',
                'content' => 'Great service and fast delivery.',
                'rating' => 4.5,
                'position' => 'Manager, Demo Corp.',
            ),
            array(
                'title' => 'Bob Johnson',
                'content' => 'Good value for money.',
                'rating' => 4,
                'position' => 'Freelancer',
            ),
        );

        foreach ($demo_reviews as $review) {
            $post_id = wp_insert_post(array(
                'post_type' => 'homepage_reviews',
                'post_title' => $review['title'],
                'post_content' => $review['content'],
                'post_status' => 'publish',
            ));

            if ($post_id) {
                update_post_meta($post_id, '_review_rating', $review['rating']);
                update_post_meta($post_id, '_reviewer_position', $review['position']);
            }
        }

        wp_safe_redirect(add_query_arg(array('page' => 'homepage_reviews_settings', 'message' => 'demo_imported'), admin_url('edit.php?post_type=homepage_reviews')));
        exit;
    }
}
