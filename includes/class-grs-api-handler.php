<?php 

/**
 * API Handler Class
 */
class TNDRESL_API_Handler {
    
    private $api_key;
    private $place_id;
    private $cache_duration = 3600; // 1 hour
    
    public function __construct() {
        $this->api_key = get_option('TRONRESL_grs_api_key');
        $this->place_id = get_option('TRONRESL_grs_place_id');
    }
    
    public function TNDRESL_get_reviews($limit = 10) {
        $cache_key = 'TRONRESL_grs_reviews_' . $this->place_id;
        $cached_reviews = get_transient($cache_key);
        
        if ($cached_reviews !== false) {
            return $cached_reviews;
        }
        
        $reviews = $this->TNDRESL_fetch_reviews_from_api($limit);
        
        if (!empty($reviews)) {
            set_transient($cache_key, $reviews, $this->cache_duration);
        }
        
        return $reviews;
    }
    
    private function TNDRESL_fetch_reviews_from_api($limit) {
        if (empty($this->api_key) || empty($this->place_id)) {
            return array();
        }
        
        $url = sprintf(
            'https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&fields=name,rating,reviews,user_ratings_total&key=%s',
            $this->place_id,
            $this->api_key
        );
        
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            return array();
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['result']['reviews'])) {
            return array_slice($data['result']['reviews'], 0, $limit);
        }
        
        return array();
    }
    
    public function TNDRESL_clear_cache() {
        $cache_key = 'grs_reviews_' . $this->place_id;
        delete_transient($cache_key);
    }
}
