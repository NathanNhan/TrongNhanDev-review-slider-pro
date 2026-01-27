<?php 
/**
 * Admin Class
 */
if (!defined('ABSPATH')) exit;

class TNDRESL_Admin {
    
    private $settings;
    
    public function __construct($settings) {
        $this->settings = $settings;
        add_action('admin_notices', array($this, 'TNDRESL_admin_notices'));
    }
    
    public function TNDRESL_admin_notices() {
        $api_key = get_option('TRONRESL_grs_api_key');
        $place_id = get_option('TRONRESL_grs_place_id');
        
        if (empty($api_key) || empty($place_id)) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong>Google Review Slider:</strong> 
                    Vui lòng cấu hình API Key và Place ID trong 
                    <a href="<?php echo esc_attr(admin_url('admin.php?page=google-review-slider')); ?>">
                        trang cài đặt
                    </a>
                </p>
            </div>
            <?php
        }
    }
}