<?php 
/**
 * Frontend Class
 */
if (!defined('ABSPATH')) exit;

class TNDRESL_Frontend_Pro {
    
    private $api_handler;
    
    public function __construct($api_handler) {
        $this->api_handler = $api_handler;
        add_shortcode('google_review_slider', array($this, 'TNDRESL_render_slider'));
    }
    
    public function TNDRESL_render_slider($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'layout' => get_option('TNDRESL_grs_layout', 'slider'),
            'columns' => get_option('TNDRESL_grs_columns', 3)
        ), $atts);
        
        $reviews = $this->api_handler->TNDRESL_get_reviews($atts['limit']);
        
        if (empty($reviews)) {
            return '<p>Không có review để hiển thị. Vui lòng kiểm tra cài đặt API.</p>';
        }
        
        ob_start();
        
        // Render dựa theo layout
        switch ($atts['layout']) {
            case 'grid':
                echo "Đã chạy được tính năng pro cho dạng grid";
                $this->TNDRESL_render_grid_html($reviews, $atts['columns']);
                break;
            case 'list':
                $this->TNDRESL_render_list_html($reviews);
                break;
            case 'slider':
            default:
                $this->TNDRESL_render_slider_html($reviews);
                break;
        }
        
        return ob_get_clean();
    }
    
    /**
     * Render Slider Layout
     */
    private function TNDRESL_render_slider_html($reviews) {
        ?>
        <div class="grs-slider-container">
            <div class="swiper grs-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($reviews as $review): ?>
                        <div class="swiper-slide">
                            <?php $this->TNDRESL_render_review_card($review); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Grid Layout
     */
    private function TNDRESL_render_grid_html($reviews, $columns = 3) {
        ?>
        <div class="grs-grid-container grs-columns-<?php echo esc_attr($columns); ?>">
            <?php foreach ($reviews as $review): ?>
                <div class="grs-grid-item">
                    <?php $this->TNDRESL_render_review_card($review); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render List Layout
     */
    private function TNDRESL_render_list_html($reviews) {
        ?>
        <div class="grs-list-container">
            <?php foreach ($reviews as $review): ?>
                <div class="grs-list-item">
                    <?php $this->TNDRESL_render_review_card($review); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render single review card
     */
    private function TNDRESL_render_review_card($review) {
        ?>
        <div class="grs-review-card">
            <div class="grs-review-header">
                <img src="<?php echo esc_url($review['profile_photo_url']); ?>" 
                     alt="<?php echo esc_attr($review['author_name']); ?>"
                     class="grs-author-photo">
                <div class="grs-author-info">
                    <h3 class="grs-author-name">
                        <?php echo esc_html($review['author_name']); ?>
                    </h3>
                    <div class="grs-rating">
                        <?php echo $this->TNDRESL_render_stars($review['rating']); ?>
                    </div>
                </div>
            </div>
            <div class="grs-review-content">
                <p><?php echo esc_html($review['text']); ?></p>
            </div>
            <div class="grs-review-footer">
                <span class="grs-review-time">
                    <?php echo esc_html($review['relative_time_description']); ?>
                </span>
                <a href="<?php echo esc_url($review['author_url']); ?>" 
                   target="_blank" 
                   class="grs-google-link">
                    <img src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__))); ?>assets/images/google-icon.png" 
                         alt="Google" style="width: 16px; height: 16px;">
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render star rating
     */
    private function TNDRESL_render_stars($rating) {
        $output = '<div class="grs-stars">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $output .= '<span class="grs-star filled">★</span>';
            } else {
                $output .= '<span class="grs-star">☆</span>';
            }
        }
        $output .= '</div>';
        return $output;
    }
}
