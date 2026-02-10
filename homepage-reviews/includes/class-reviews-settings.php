<?php
if (!defined('ABSPATH')) {
    exit;
}

class Homepage_Reviews_Settings
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=homepage_reviews',
            __('Reviews Settings', 'homepage-reviews'),
            __('Settings', 'homepage-reviews'),
            'manage_options',
            'homepage_reviews_settings',
            array($this, 'settings_page_html')
        );
    }

    public function register_settings()
    {
        register_setting('homepage_reviews_settings', 'homepage_reviews_settings');

        add_settings_section(
            'homepage_reviews_style_section',
            __('Style Settings', 'homepage-reviews'),
            null,
            'homepage_reviews_settings'
        );

        add_settings_field(
            'card_bg_color',
            __('Card Background Color', 'homepage-reviews'),
            array($this, 'color_picker_callback'),
            'homepage_reviews_settings',
            'homepage_reviews_style_section',
            array('field' => 'card_bg_color', 'default' => '#ffffff')
        );

        add_settings_field(
            'text_color',
            __('Text Color', 'homepage-reviews'),
            array($this, 'color_picker_callback'),
            'homepage_reviews_settings',
            'homepage_reviews_style_section',
            array('field' => 'text_color', 'default' => '#333333')
        );

        add_settings_field(
            'rating_color',
            __('Rating Star Color', 'homepage-reviews'),
            array($this, 'color_picker_callback'),
            'homepage_reviews_settings',
            'homepage_reviews_style_section',
            array('field' => 'rating_color', 'default' => '#ffb900')
        );

        add_settings_field(
            'font_size',
            __('Font Size (px)', 'homepage-reviews'),
            array($this, 'number_field_callback'),
            'homepage_reviews_settings',
            'homepage_reviews_style_section',
            array('field' => 'font_size', 'default' => '16')
        );

        add_settings_field(
            'border_radius',
            __('Border Radius (px)', 'homepage-reviews'),
            array($this, 'number_field_callback'),
            'homepage_reviews_settings',
            'homepage_reviews_style_section',
            array('field' => 'border_radius', 'default' => '8')
        );
    }

    public function color_picker_callback($args)
    {
        $options = get_option('homepage_reviews_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<input type="color" name="homepage_reviews_settings[' . esc_attr($args['field']) . ']" value="' . esc_attr($value) . '">';
    }

    public function number_field_callback($args)
    {
        $options = get_option('homepage_reviews_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<input type="number" name="homepage_reviews_settings[' . esc_attr($args['field']) . ']" value="' . esc_attr($value) . '" style="width: 60px;"> px';
    }

    public function settings_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>
                <?php echo esc_html(get_admin_page_title()); ?>
            </h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('homepage_reviews_settings');
                do_settings_sections('homepage_reviews_settings');
                submit_button('Save Settings');
                ?>
            </form>

            <hr>
            <h2><?php _e('Demo Data', 'homepage-reviews'); ?></h2>
            <p><?php _e('Import sample reviews to see how the plugin looks.', 'homepage-reviews'); ?></p>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                <input type="hidden" name="action" value="homepage_reviews_import_demo_data">
                <?php wp_nonce_field('homepage_reviews_import_demo_data_action', 'homepage_reviews_import_demo_data_nonce'); ?>
                <?php submit_button(__('Import Demo Reviews', 'homepage-reviews'), 'secondary'); ?>
            </form>
        </div>
        <?php
    }
}
