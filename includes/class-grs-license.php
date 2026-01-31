<?php
/**
 * License Manager Class
 * Quản lý license key cho TrongNhanDev Review Slider Pro
 */

if (!defined('ABSPATH')) exit;

class TNDRESL_License_Manager {
    
    private $plugin_name = 'TrongNhanDev Review Slider Pro';
    private $plugin_version = '1.5';
    private $option_name = 'tndresl_license_key';
    private $api_url = 'https://trongnhandev.com/api/verify-license'; // Thay bằng URL API của bạn
    
    public function __construct() {
        // Hook vào admin menu
        add_action('admin_menu', array($this, 'add_license_menu'));
        
        // Hook để xử lý form submit
        add_action('admin_init', array($this, 'handle_license_form'));
        
        // Thêm admin notice nếu chưa kích hoạt
        add_action('admin_notices', array($this, 'license_admin_notice'));
        
        // Thêm link Settings vào plugin list
        add_filter('plugin_action_links_' . plugin_basename(TRONRESL_GRS_PATH . 'TrongNhanDev-review-slider.php'), array($this, 'add_settings_link'));
    }
    
    /**
     * Thêm submenu License vào menu Google Reviews
     */
    public function add_license_menu() {
        add_submenu_page(
            'google-review-slider',
            'License Activation',
            'License',
            'manage_options',
            'tndresl-license',
            array($this, 'render_license_page')
        );
    }
    
    /**
     * Thêm link License vào danh sách plugin
     */
    public function add_settings_link($links) {
        $license_link = '<a href="' . admin_url('admin.php?page=tndresl-license') . '" style="color: #d63638; font-weight: bold;">Activate License</a>';
        
        if (!$this->is_license_active()) {
            array_unshift($links, $license_link);
        }
        
        return $links;
    }
    
    /**
     * Hiển thị trang License settings
     */
    public function render_license_page() {
        $license_key = get_option($this->option_name, '');
        $is_active = get_option($this->option_name . '_status', false);
        $activated_time = get_option($this->option_name . '_activated_time', 0);
        $domain = get_site_url();
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html($this->plugin_name); ?> - License Activation</h1>
            
            <?php if (isset($_GET['activated']) && $_GET['activated'] == 'true'): ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong>✓ Thành công!</strong> License key đã được kích hoạt.</p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['deactivated']) && $_GET['deactivated'] == 'true'): ?>
                <div class="notice notice-success is-dismissible">
                    <p>License key đã được hủy kích hoạt.</p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="notice notice-error is-dismissible">
                    <p><strong>Lỗi:</strong> <?php echo esc_html(urldecode($_GET['error'])); ?></p>
                </div>
            <?php endif; ?>
            
            <div style="background: #fff; padding: 20px; margin-top: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                
                <?php if ($is_active): ?>
                    <!-- License Active State -->
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <h2 style="margin-top: 0; color: #155724;">
                            <span class="dashicons dashicons-yes-alt" style="color: #28a745; font-size: 24px;"></span>
                            License Đã Kích Hoạt
                        </h2>
                        <p style="margin: 10px 0;">Plugin của bạn đang hoạt động bình thường.</p>
                    </div>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">License Key</th>
                            <td>
                                <input type="text" 
                                       value="<?php echo esc_attr($this->mask_license_key($license_key)); ?>" 
                                       class="regular-text" 
                                       readonly 
                                       style="background: #f0f0f1;">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Trạng Thái</th>
                            <td>
                                <span style="color: #28a745; font-weight: bold;">
                                    <span class="dashicons dashicons-yes"></span> Đã Kích Hoạt
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Domain</th>
                            <td><code><?php echo esc_html($domain); ?></code></td>
                        </tr>
                        <tr>
                            <th scope="row">Ngày Kích Hoạt</th>
                            <td><?php echo $activated_time ? date('d/m/Y H:i:s', $activated_time) : 'N/A'; ?></td>
                        </tr>
                    </table>
                    
                    <form method="post" action="" style="margin-top: 20px;">
                        <?php wp_nonce_field('tndresl_license_action', 'tndresl_license_nonce'); ?>
                        <input type="submit" 
                               name="deactivate_license" 
                               class="button button-secondary" 
                               value="Hủy Kích Hoạt License"
                               onclick="return confirm('Bạn có chắc muốn hủy kích hoạt license? Plugin sẽ ngừng hoạt động.');">
                    </form>
                    
                <?php else: ?>
                    <!-- License Inactive State -->
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <h2 style="margin-top: 0; color: #856404;">
                            <span class="dashicons dashicons-warning" style="color: #ff9800; font-size: 24px;"></span>
                            License Chưa Kích Hoạt
                        </h2>
                        <p style="margin: 10px 0;">Vui lòng nhập license key để sử dụng plugin.</p>
                    </div>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field('tndresl_license_action', 'tndresl_license_nonce'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="license_key">License Key <span style="color: red;">*</span></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="license_key" 
                                           name="license_key" 
                                           value="<?php echo esc_attr($license_key); ?>" 
                                           class="regular-text"
                                           placeholder="XXXX-XXXX-XXXX-XXXX"
                                           required>
                                    <p class="description">
                                        Nhập license key đã mua từ TrongNhanDev.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Domain</th>
                                <td>
                                    <code><?php echo esc_html($domain); ?></code>
                                    <p class="description">License sẽ được kích hoạt cho domain này.</p>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <input type="submit" 
                                   name="activate_license" 
                                   class="button button-primary button-large" 
                                   value="Kích Hoạt License">
                        </p>
                    </form>
                    
                    <hr style="margin: 30px 0;">
                    
                    <div style="background: #f0f6fc; padding: 15px; border-left: 4px solid #0073aa;">
                        <h3 style="margin-top: 0;">💡 Hướng Dẫn</h3>
                        <ul>
                            <li>Mỗi license key chỉ có thể kích hoạt cho 1 domain duy nhất</li>
                            <li>Nếu bạn chưa có license, vui lòng liên hệ: <a href="mailto:support@trongnhandev.com">support@trongnhandev.com</a></li>
                            <li>License key có dạng: <code>XXXX-XXXX-XXXX-XXXX</code></li>
                            <li>Sau khi kích hoạt, plugin sẽ hoạt động đầy đủ tính năng</li>
                        </ul>
                    </div>
                <?php endif; ?>
                
            </div>
            
            <!-- Support Info -->
            <div style="margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4;">
                <h3>📧 Hỗ Trợ</h3>
                <p>Nếu gặp vấn đề về license, vui lòng liên hệ:</p>
                <ul>
                    <li>Email: <a href="mailto:support@trongnhandev.com">support@trongnhandev.com</a></li>
                    <li>Website: <a href="https://trongnhandev.com" target="_blank">https://trongnhandev.com</a></li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Ẩn một phần license key
     */
    private function mask_license_key($key) {
        if (empty($key)) return '';
        
        $parts = explode('-', $key);
        if (count($parts) !== 4) return $key;
        
        return $parts[0] . '-****-****-' . $parts[3];
    }
    
    /**
     * Xử lý form submit
     */
    public function handle_license_form() {
        if (!isset($_POST['tndresl_license_nonce']) || 
            !wp_verify_nonce($_POST['tndresl_license_nonce'], 'tndresl_license_action')) {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Kích hoạt license
        if (isset($_POST['activate_license'])) {
            $license_key = sanitize_text_field($_POST['license_key']);
            
            if (empty($license_key)) {
                wp_redirect(add_query_arg('error', urlencode('Vui lòng nhập license key'), admin_url('admin.php?page=tndresl-license')));
                exit;
            }
            
            // Xác thực license với server
            $verification = $this->verify_license($license_key);
            
            if ($verification['valid']) {
                update_option($this->option_name, $license_key);
                update_option($this->option_name . '_status', true);
                update_option($this->option_name . '_activated_time', time());
                update_option($this->option_name . '_data', $verification['data']);
                
                wp_redirect(add_query_arg('activated', 'true', admin_url('admin.php?page=tndresl-license')));
                exit;
            } else {
                wp_redirect(add_query_arg('error', urlencode($verification['message']), admin_url('admin.php?page=tndresl-license')));
                exit;
            }
        }
        
        // Hủy kích hoạt license
        if (isset($_POST['deactivate_license'])) {
            $this->deactivate_license();
            
            wp_redirect(add_query_arg('deactivated', 'true', admin_url('admin.php?page=tndresl-license')));
            exit;
        }
    }
    
    /**
     * Xác thực license key với server
     */
    private function verify_license($license_key) {
        // Phương pháp 1: Xác thực với API server (Recommended)
        $response = wp_remote_post($this->api_url, array(
            'body' => array(
                'license_key' => $license_key,
                'domain' => get_site_url(),
                'plugin' => $this->plugin_name,
                'version' => $this->plugin_version,
                'action' => 'activate'
            ),
            'timeout' => 15,
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            // Nếu không kết nối được server, sử dụng offline validation
            return $this->offline_verify_license($license_key);
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['valid']) && $data['valid'] === true) {
            return array(
                'valid' => true,
                'message' => 'License hợp lệ',
                'data' => $data
            );
        }
        
        return array(
            'valid' => false,
            'message' => isset($data['message']) ? $data['message'] : 'License key không hợp lệ',
            'data' => null
        );
        
        // Phương pháp 2: Nếu chưa có API server, dùng offline validation
        // return $this->offline_verify_license($license_key);
    }
    
    /**
     * Xác thực offline (backup method)
     * Phương pháp này dùng khi không có API server
     */
    private function offline_verify_license($license_key) {
        // Kiểm tra format: XXXX-XXXX-XXXX-XXXX
        $pattern = '/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/';
        
        if (!preg_match($pattern, $license_key)) {
            return array(
                'valid' => false,
                'message' => 'Format license key không đúng. Phải có dạng: XXXX-XXXX-XXXX-XXXX',
                'data' => null
            );
        }
        
        // Phương pháp mã hóa đơn giản (có thể nâng cấp)
        // Tạo hash từ license key + secret + domain
        $secret = 'TNDRESL_SECRET_2024'; // Thay bằng secret key của bạn
        $domain = get_site_url();
        
        // Kiểm tra checksum (phần cuối của license)
        $parts = explode('-', $license_key);
        $checksum = $parts[3];
        
        // Tính checksum mong đợi
        $expected = strtoupper(substr(md5($parts[0] . $parts[1] . $parts[2] . $secret), 0, 4));
        
        if ($checksum === $expected) {
            return array(
                'valid' => true,
                'message' => 'License hợp lệ (offline mode)',
                'data' => array('mode' => 'offline')
            );
        }
        
        return array(
            'valid' => false,
            'message' => 'License key không hợp lệ',
            'data' => null
        );
    }
    
    /**
     * Hủy kích hoạt license
     */
    public function deactivate_license() {
        $license_key = get_option($this->option_name, '');
        
        // Thông báo server về việc deactivate (nếu có API)
        if (!empty($license_key)) {
            wp_remote_post($this->api_url, array(
                'body' => array(
                    'license_key' => $license_key,
                    'domain' => get_site_url(),
                    'action' => 'deactivate'
                ),
                'timeout' => 10
            ));
        }
        
        delete_option($this->option_name);
        delete_option($this->option_name . '_status');
        delete_option($this->option_name . '_activated_time');
        delete_option($this->option_name . '_data');
    }
    
    /**
     * Hiển thị thông báo admin nếu chưa kích hoạt
     */
    public function license_admin_notice() {
        $is_active = $this->is_license_active();
        
        // Chỉ hiển thị trên trang admin của plugin
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'google-review') === false) {
            return;
        }
        
        if (!$is_active) {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php echo esc_html($this->plugin_name); ?></strong> chưa được kích hoạt. 
                    Plugin sẽ không hoạt động cho đến khi bạn nhập license key hợp lệ.
                    <a href="<?php echo admin_url('admin.php?page=tndresl-license'); ?>" class="button button-primary" style="margin-left: 10px;">
                        Kích Hoạt Ngay
                    </a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * Kiểm tra xem license có đang active không
     */
    public function is_license_active() {
        return (bool) get_option($this->option_name . '_status', false);
    }
    
    /**
     * Lấy thông tin license
     */
    public function get_license_info() {
        return array(
            'key' => get_option($this->option_name, ''),
            'status' => $this->is_license_active(),
            'activated_time' => get_option($this->option_name . '_activated_time', 0),
            'data' => get_option($this->option_name . '_data', array())
        );
    }
}
