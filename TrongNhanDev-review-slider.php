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

define('TRONRESL_GRS_VERSION', '1.4');
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
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self(); // new Google_Review_Slider()
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_components();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once TRONRESL_GRS_PATH . 'includes/class-grs-api-handler.php';
        require_once TRONRESL_GRS_PATH . 'includes/class-grs-settings.php';
        require_once TRONRESL_GRS_PATH . 'includes/class-grs-frontend.php';
        require_once TRONRESL_GRS_PATH . 'includes/class-grs-admin.php';
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
        wp_enqueue_style('tndresl-swiper-css', plugin_dir_url( __FILE__ ) . "/assets/css/swiper-bundle.min.css", array(), random_int(0,99), 'all');
        wp_enqueue_style('tndresl-grs-style', TRONRESL_GRS_URL . 'assets/css/style.css', array(), TRONRESL_GRS_VERSION);
        
        wp_enqueue_script('tndresl-swiper-js', plugin_dir_url( __FILE__ ) . "/assets/js/swiper-bundle.min.js", array(), '11.0.0', true);
        wp_enqueue_script('tndresl-slider', TRONRESL_GRS_URL . 'assets/js/slider.js', array('swiper-js'), TRONRESL_GRS_VERSION, true);
    }
    
    public function activate() {
        add_option('TNDRESL_grs_api_key', '');
        add_option('TNDRESL_grs_place_id', '');
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }

}
// Initialize plugin
function TNDRESL_grs_init() {
    return TNDRESL_Review_Slider::get_instance();
}

add_action('plugins_loaded', 'TNDRESL_grs_init');
//find place id
// https://developers.google.com/maps/documentation/places/web-service/place-id#find-id