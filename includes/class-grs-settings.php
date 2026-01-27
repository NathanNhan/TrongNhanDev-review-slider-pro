<?php 

/**
 * Settings Class
 */
if (!defined('ABSPATH')) exit;

class TNDRESL_Settings {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'TNDRESL_add_menu_page'));
        add_action('admin_init', array($this, 'TNDRESL_register_settings'));
    }
    
    public function TNDRESL_add_menu_page() {
        add_menu_page(
            'Google Review Slider',
            'Google Reviews',
            'manage_options',
            'google-review-slider',
            array($this, 'TNDRESL_render_settings_page'),
            'dashicons-star-filled',
            30
        );
    }
    
    public function TNDRESL_register_settings() {
        // Register API Key
        register_setting('TNDRESL_grs_settings', 'TNDRESL_grs_api_key', array(
            'sanitize_callback' => 'sanitize_text_field',
        ));

        // Register Place ID
        register_setting('TNDRESL_grs_settings', 'TNDRESL_grs_place_id', array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        // Register Layout Setting
        register_setting('TNDRESL_grs_settings', 'TNDRESL_grs_layout', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'slider'
        ));
        
        // Register Columns Setting
        register_setting('TNDRESL_grs_settings', 'TNDRESL_grs_columns', array(
            'sanitize_callback' => 'absint',
            'default' => 3
        ));
        
        // API Settings Section
        add_settings_section(
            'TNDRESL_grs_main_section',
            'Cài Đặt API',
            array($this, 'TNDRESL_section_callback'),
            'google-review-slider'
        );
        
        add_settings_field(
            'TNDRESL_grs_api_key',
            'Google API Key',
            array($this, 'TNDRESL_api_key_callback'),
            'google-review-slider',
            'TNDRESL_grs_main_section'
        );
        
        add_settings_field(
            'TNDRESL_grs_place_id',
            'Google Place ID',
            array($this, 'TNDRESL_place_id_callback'),
            'google-review-slider',
            'TNDRESL_grs_main_section'
        );
        
        // Display Settings Section
        add_settings_section(
            'TNDRESL_grs_display_section',
            'Cài Đặt Hiển Thị',
            array($this, 'TNDRESL_display_section_callback'),
            'google-review-slider'
        );
        
        add_settings_field(
            'TNDRESL_grs_layout',
            'Kiểu Hiển Thị',
            array($this, 'TNDRESL_layout_callback'),
            'google-review-slider',
            'TNDRESL_grs_display_section'
        );
        
        add_settings_field(
            'TNDRESL_grs_columns',
            'Số Cột (Grid Layout)',
            array($this, 'TNDRESL_columns_callback'),
            'google-review-slider',
            'TNDRESL_grs_display_section'
        );
    }
    
    public function TNDRESL_section_callback() {
        echo '<p>Nhập thông tin API để kết nối với Google Places API</p>';
        echo '<p><a href="https://developers.google.com/maps/documentation/places/web-service/get-api-key" target="_blank">Hướng dẫn lấy API Key</a></p>';
    }
    
    public function TNDRESL_display_section_callback() {
        echo '<p>Tùy chỉnh cách hiển thị reviews trên website</p>';
    }
    
    public function TNDRESL_api_key_callback() {
        $value = get_option('TNDRESL_grs_api_key');
        printf(
            '<input type="text" name="TNDRESL_grs_api_key" value="%s" class="regular-text" />',
            esc_attr($value)
        );
    }
    
    public function TNDRESL_place_id_callback() {
        $value = get_option('TNDRESL_grs_place_id');
        printf(
            '<input type="text" name="TNDRESL_grs_place_id" value="%s" class="regular-text" />',
            esc_attr($value)
        );
        echo '<p class="description">Ví dụ: ChIJN1t_tDeuEmsRUsoyG83frY4</p>';
    }
    
    public function TNDRESL_layout_callback() {
        $value = get_option('TNDRESL_grs_layout', 'slider');
        $layouts = array(
            'slider' => 'Slider (Swiper)',
            'grid' => 'Grid (Lưới cột)',
            'list' => 'List (Danh sách)'
        );
        
        echo '<select name="TNDRESL_grs_layout" id="TNDRESL_grs_layout">';
        foreach ($layouts as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
        echo '<p class="description">Chọn kiểu hiển thị reviews</p>';
        
        // JavaScript to show/hide columns option
        ?>
        <script>
        jQuery(document).ready(function($) {
            function toggleColumnsField() {
                var layout = $('#TNDRESL_grs_layout').val();
                var columnsRow = $('#TNDRESL_grs_columns').closest('tr');
                
                if (layout === 'grid') {
                    columnsRow.show();
                } else {
                    columnsRow.hide();
                }
            }
            
            toggleColumnsField();
            $('#TNDRESL_grs_layout').on('change', toggleColumnsField);
        });
        </script>
        <?php
    }
    
    public function TNDRESL_columns_callback() {
        $value = get_option('TNDRESL_grs_columns', 3);
        ?>
        <select name="TNDRESL_grs_columns" id="TNDRESL_grs_columns">
            <option value="2" <?php selected($value, 2); ?>>2 cột</option>
            <option value="3" <?php selected($value, 3); ?>>3 cột</option>
            <option value="4" <?php selected($value, 4); ?>>4 cột</option>
            <option value="5" <?php selected($value, 5); ?>>5 cột</option>
        </select>
        <p class="description">Số cột hiển thị khi chọn Grid Layout</p>
        <?php
    }
    
    public function TNDRESL_render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Google Review Slider Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('TNDRESL_grs_settings');
                do_settings_sections('google-review-slider');
                submit_button();
                ?>
            </form>
            
            <hr>
            
            <h2>Shortcode</h2>
            <p>Sử dụng shortcode sau để hiển thị reviews:</p>
            <code>[google_review_slider limit="10"]</code>
            
            <h3>Các tham số:</h3>
            <ul>
                <li><strong>limit</strong>: Số lượng review hiển thị (mặc định: 10)</li>
                <li><strong>layout</strong>: Kiểu hiển thị (slider, grid, list) - ghi đè setting</li>
                <li><strong>columns</strong>: Số cột cho grid layout (2-5) - ghi đè setting</li>
            </ul>
            
            <h3>Ví dụ:</h3>
            <ul>
                <li><code>[google_review_slider limit="12" layout="grid" columns="4"]</code></li>
                <li><code>[google_review_slider limit="6" layout="list"]</code></li>
            </ul>
        </div>
        <?php
    }
}
