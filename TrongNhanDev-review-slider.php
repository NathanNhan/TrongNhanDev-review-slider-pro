<?php
/**
 * Plugin Name: Trongnhandev Review Slider Pro
 * Plugin URI: https://trongnhandev.com
 * Description: Display TrongNhanDev Review Slider via Google place API.
 * Version: 1.5
 * Author: Trong Nhan Dev
 * License: GPL2
 * Text Domain: tnd-review-slider-pro
 */

if (!defined('ABSPATH')) exit;

define('TRONRESL_GRS_VERSION', '1.5');
define('TRONRESL_GRS_PATH', plugin_dir_path(__FILE__));
define('TRONRESL_GRS_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
class TNDRESL_Review_Slider {
    
    private static $instance = null;
    private $api_handler;
    private $settings;
    private $frontend;
    private $license_manager;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_license();
        
        // Chỉ khởi tạo plugin nếu license đã active
        if ($this->is_license_valid()) {
            $this->init_components();
            $this->init_hooks();
        } else {
            // Nếu chưa có license, chỉ hiển thị thông báo
            add_action('admin_notices', array($this, 'license_required_notice'));
        }
    }
    
    private function load_dependencies() {
        // Load License Manager trước tiên
        require_once TRONRESL_GRS_PATH . 'includes/class-grs-license.php';
        
        require_once TRONRESL_GRS_PATH . 'includes/class-grs-api-handler.php';
        require_once TRONRESL_GRS_PATH . 'includes/class-grs-settings.php';
        require_once TRONRESL_GRS_PATH . 'includes/class-grs-frontend.php';
        require_once TRONRESL_GRS_PATH . 'includes/class-grs-admin.php';
    }
    
    private function init_license() {
        // Khởi tạo License Manager (luôn chạy)
        $this->license_manager = new TNDRESL_License_Manager();
    }
    
    private function is_license_valid() {
        return $this->license_manager->is_license_active();
    }
    
    private function init_components() {
        $this->api_handler = new TNDRESL_API_Handler();
        $this->settings = new TNDRESL_Settings();
        $this->frontend = new TNDRESL_Frontend($this->api_handler);
        new TNDRESL_Admin($this->settings);
    }
    
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function enqueue_scripts() {
        // Chỉ load scripts nếu license active
        if (!$this->is_license_valid()) {
            return;
        }
        
        wp_enqueue_style('tndresl-swiper-css', plugin_dir_url( __FILE__ ) . "/assets/css/swiper-bundle.min.css", array(), '11.0.0', 'all');
        wp_enqueue_style('tndresl-grs-style', TRONRESL_GRS_URL . 'assets/css/style.css', array(), TRONRESL_GRS_VERSION);
        
        wp_enqueue_script('tndresl-swiper-js', plugin_dir_url( __FILE__ ) . "/assets/js/swiper-bundle.min.js", array(), '11.0.0', true);
        wp_enqueue_script('tndresl-slider', TRONRESL_GRS_URL . 'assets/js/slider.js', array('tndresl-swiper-js'), TRONRESL_GRS_VERSION, true);
    }
    
    public function activate() {
        add_option('TNDRESL_grs_api_key', '');
        add_option('TNDRESL_grs_place_id', '');
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Thông báo yêu cầu kích hoạt license
     */
    public function license_required_notice() {
        // Không hiển thị trên trang license
        if (isset($_GET['page']) && $_GET['page'] === 'tndresl-license') {
            return;
        }
        
        ?>
        <div class="notice notice-error">
            <p>
                <strong>TrongNhanDev Review Slider Pro</strong> yêu cầu kích hoạt license để sử dụng. 
                <a href="<?php echo admin_url('admin.php?page=tndresl-license'); ?>" class="button button-primary" style="margin-left: 10px;">
                    Kích Hoạt License
                </a>
            </p>
        </div>
        <?php
    }
    
    /**
     * Get License Manager instance
     */
    public function get_license_manager() {
        return $this->license_manager;
    }

}

// Initialize plugin
function TNDRESL_grs_init() {
    return TNDRESL_Review_Slider::get_instance();
}

add_action('plugins_loaded', 'TNDRESL_grs_init');

//find place id
// https://developers.google.com/maps/documentation/places/web-service/place-id#find-id
