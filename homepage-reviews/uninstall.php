<?php
/**
 * Fired when the plugin is uninstalled (deleted).
 *
 * @package Homepage_Reviews
 */

// If uninstall not called from WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin settings.
delete_option('homepage_reviews_settings');

// Delete all review posts and their meta.
$homepage_reviews_posts = get_posts(array(
    'post_type' => 'homepage_reviews',
    'posts_per_page' => -1,
    'post_status' => 'any',
    'fields' => 'ids',
));

if (!empty($homepage_reviews_posts)) {
    foreach ($homepage_reviews_posts as $homepage_reviews_id) {
        wp_delete_post($homepage_reviews_id, true);
    }
}
