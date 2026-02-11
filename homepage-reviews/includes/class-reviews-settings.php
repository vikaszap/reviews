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
        register_setting('homepage_reviews_settings', 'homepage_reviews_settings', array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));


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

        // Slider Settings Section
        add_settings_section(
            'homepage_reviews_slider_section',
            __('Slider Settings', 'homepage-reviews'),
            null,
            'homepage_reviews_settings'
        );

        add_settings_field(
            'autoplay',
            __('Enable Autoplay', 'homepage-reviews'),
            array($this, 'checkbox_field_callback'),
            'homepage_reviews_settings',
            'homepage_reviews_slider_section',
            array('field' => 'autoplay', 'default' => '0', 'label' => __('Automatically advance slides', 'homepage-reviews'))
        );

        add_settings_field(
            'autoplay_speed',
            __('Autoplay Speed (ms)', 'homepage-reviews'),
            array($this, 'number_field_callback'),
            'homepage_reviews_settings',
            'homepage_reviews_slider_section',
            array('field' => 'autoplay_speed', 'default' => '5000')
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
        echo '<input type="number" name="homepage_reviews_settings[' . esc_attr($args['field']) . ']" value="' . esc_attr($value) . '" style="width: 80px;">';
    }

    public function checkbox_field_callback($args)
    {
        $options = get_option('homepage_reviews_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        $label = isset($args['label']) ? $args['label'] : '';
        echo '<label>';
        echo '<input type="checkbox" name="homepage_reviews_settings[' . esc_attr($args['field']) . ']" value="1" ' . checked($value, '1', false) . ' />';
        echo ' ' . esc_html($label);
        echo '</label>';
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
                submit_button(__('Save Settings', 'homepage-reviews'));
                ?>
            </form>

            <hr>
            <h2><?php esc_html_e('Demo Data', 'homepage-reviews'); ?></h2>
            <p><?php esc_html_e('Import sample reviews to see how the plugin looks.', 'homepage-reviews'); ?></p>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="homepage_reviews_import_demo_data">
                <?php wp_nonce_field('homepage_reviews_import_demo_data_action', 'homepage_reviews_import_demo_data_nonce'); ?>
                <?php submit_button(__('Import Demo Reviews', 'homepage-reviews'), 'secondary'); ?>
            </form>
        </div>
        <?php
    }

    public function sanitize_settings($input)
    {
        $new_input = array();

        if (isset($input['card_bg_color'])) {
            $new_input['card_bg_color'] = sanitize_hex_color($input['card_bg_color']);
        }

        if (isset($input['text_color'])) {
            $new_input['text_color'] = sanitize_hex_color($input['text_color']);
        }

        if (isset($input['rating_color'])) {
            $new_input['rating_color'] = sanitize_hex_color($input['rating_color']);
        }

        if (isset($input['font_size'])) {
            $new_input['font_size'] = absint($input['font_size']);
        }

        if (isset($input['border_radius'])) {
            $new_input['border_radius'] = absint($input['border_radius']);
        }

        if (isset($input['autoplay'])) {
            $new_input['autoplay'] = ($input['autoplay'] === '1') ? '1' : '0';
        }

        if (isset($input['autoplay_speed'])) {
            $new_input['autoplay_speed'] = absint($input['autoplay_speed']);
        }

        return $new_input;
    }
}
