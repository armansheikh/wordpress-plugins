<?php

/**
 * The API code for public-facing functionality of the plugin.
 *
 * @link       https://esipick.com/
 * @since      1.0.0
 *
 * @package    Im_Deals_Vue_Filters
 * @subpackage Im_Deals_Vue_Filters/includes
 * @author     Arman Sheikh <armansheikh92@gmail.com>
 */
class Im_Deals_Vue_Filters_Api
{
    public $filters = [];

    public function registerApiRoutes()
    {
        add_action('rest_api_init', function () {
            register_rest_route(
                'rest',
                '/search/deals/',
                array(
                    'methods' => 'GET',
                    'callback' => [$this, 'getFilteredDeals'],
                )
            );

            register_rest_route(
                'rest',
                '/deals/pinned',
                array(
                    'methods' => 'GET',
                    'callback' => [$this, 'gePinnedDeals'],
                )
            );

            register_rest_route(
                'rest',
                '/deals/suggestion',
                array(
                    'methods' => 'GET',
                    'callback' => [$this, 'getDealsSuggestion'],
                )
            );

            register_rest_route(
                'rest',
                '/deals/filters/',
                array(
                    'methods' => 'GET',
                    'callback' => [$this, 'getDealsFilters'],
                )
            );
        });
    }

    public function getFilteredDeals($request)
    {

        $response = ['status' => true, 'data' => [], 'message' => 'success', 'status_code' => 200, 'total_records' => 0, 'total_pages' => 0, 'page' => 1];

        global $wpdb;

        $sortBy = isset($request['sort_by']) ? $request['sort_by'] : 'post_date';
        $sortOrder = 'desc';
        $categoryFilter = json_decode($request['categories'], true);
        $dealTypeFilter = json_decode($request['deal_types'], true);
        $page = isset($request['page']) ? $request['page'] : 1;
        $limit = isset($request['page_limit']) ? $request['page_limit'] : 12;
        $keyword = str_replace('"', '', urldecode($request['keyword']));
        $offset = 0;
        $itemsCount = 12;
        $dealIds = [];
        $popularDealCount = 0;
        //get pinned deal ids
        $pinnedDealsIds = $this->gePinnedDealIds();
        $pinnedDealCount = count($pinnedDealsIds);

        $args = array(
            'post_type' => array('imdeals'),
            'post_status' => array('publish'),
            'posts_per_page' => $limit,
            'meta_query' => [],
            'tax_query' => [],
            'paged' => $page
        );

        $response['page'] = $page;

        if ($sortBy == 'post_date') {
            $args['orderby'] = $sortBy;
        } elseif ($sortBy == 'claim_count') {
            global $popularFilter;

            $popularFilter = $request['popular_deals'];
            add_filter('posts_orderby', array('Im_Deals_Vue_Filters_Api', 'edit_posts_orderby'));

            add_filter('posts_join_paged', array('Im_Deals_Vue_Filters_Api', 'edit_posts_join_paged'));

            add_filter('posts_groupby', array('Im_Deals_Vue_Filters_Api', 'query_group_by_filter'));
        }

        if (isset($request['popular_deals']) && !empty($request['popular_deals'])) {
            $dealIds = $this->getTopDeals($request['popular_deals']);
            $popularDealCount = count($dealIds);
            
            // if ($pinnedDealCount > 0) {
            //     $dealIds = array_filter($dealIds, fn ($e) => !in_array($e, $pinnedDealsIds));
            //     if ($page == 1) {
            //         $limit = $limit - $pinnedDealCount;
            //     }
            // }

            //calculate offset to handle duplicate issue
            $offset = ($page - 1) * $limit;
            $newDealIds = array_slice($dealIds, $offset, $limit);

            //to handle if dealids don't retuns set fake post id other wise all posts will be returned
            $args['post__in'] = count($newDealIds) > 0 ? $newDealIds : ['p1'];
            $args['orderby'] = 'post__in';


            $limit = 500;
            $page = 1;
        }
        $isRelationAnd = false;
        if (!empty($keyword)) {
            $keyword = wp_unslash($keyword);
            $keyword = sanitize_text_field($keyword);

            $isRelationAnd = true;

            if ($brandFound = $this->checkKeyworyRelevance($keyword, 'brands')) {

                $args['tax_query'][] = [
                    'taxonomy' => 'brands',
                    'field' => 'name',
                    'terms' => $brandFound,
                    'compare' => '='
                ];
            } elseif ($categoryFound = $this->checkKeyworyRelevance($keyword, 'deal_category')) {

                $categoryFilter[] = $categoryFound;
            } elseif ($keywordFound = $this->checkKeyworyRelevance($keyword, 'post_tag')) {
                //for keyword searching
                $args['tax_query'][] = [
                    'taxonomy'     => 'post_tag',
                    'field' => 'name',
                    'terms'   => $keywordFound,
                    'compare' => '=',
                ];
            } else {
                //$args['s'] = $keyword;
                $args['meta_query'][] = [
                    'key' => 'description',
                    'value' => $keyword,
                    'compare' => 'LIKE'
                ];

                $args['meta_query'][] = [
                    'key' => 'short_description',
                    'value' => $keyword,
                    'compare' => 'LIKE'
                ];

                //deal snapshot
                $args['meta_query'][] = [
                    'key' => 'deal_snapshot_0_information',
                    'value' => $keyword,
                    'compare' => 'LIKE'
                ];

                $args['meta_query'][] = [
                    'key' => 'deal_snapshot_1_information',
                    'value' => $keyword,
                    'compare' => 'LIKE'
                ];

                $args['meta_query'][] = [
                    'key' => 'deal_snapshot_2_information',
                    'value' => $keyword,
                    'compare' => 'LIKE'
                ];

                $args['meta_query'][] = [
                    'key' => 'deal_snapshot_3_information',
                    'value' => $keyword,
                    'compare' => 'LIKE'
                ];

                $args['meta_query']['relation'] = 'OR';
            }
        }

        if (count($dealTypeFilter) > 0) {

            if (!in_array('All [*]', $dealTypeFilter)) {
                foreach ($dealTypeFilter as $dealType) {
                    if ($dealType == 'Expired') {
                        $args['meta_query'][] = [
                            'key' => 'deal_expired',
                            'value' => 1,
                            'compare' => '='
                        ];
                    } elseif ($dealType == 'Ending Soon') {
                        $args['meta_query'][] = [
                            'key' => 'limited_time_deal',
                            'value' => '',
                            'compare' => '!='
                        ];
                    } else {
                        $args['meta_query'][] = [
                            'key' => 'deal_type',
                            'value' => $dealType,
                            'compare' => 'LIKE'
                        ];
                    }
                }
                if (count($args['meta_query']) > 1) {
                    if ($isRelationAnd)
                        $args['meta_query']['relation'] = 'AND';
                    else
                        $args['meta_query']['relation'] = 'OR';
                }
            }
        }

        if (count($categoryFilter) > 0) {

            $args['tax_query'][] = [
                'taxonomy' => 'deal_category',
                'field' => 'name',
                'terms' => $categoryFilter,
                'compare' => 'IN'
            ];
        }

        //set query pagination
        $args['posts_per_page'] = $limit;
        $args['paged'] = $page;
        $records = [];


        if ($pinnedDealCount > 0 && empty($request['popular_deals'])) {
            if ($page == 1) {
                $args['posts_per_page'] = $limit - $pinnedDealCount;
            }

            if (empty($keyword) && count($dealTypeFilter) == 0 && count($categoryFilter) == 0) {
                $args['post__not_in'] = $pinnedDealsIds;
            }
        }

        $posts = new WP_Query($args);

        //pagination
        if (isset($request['popular_deals']) && !empty($request['popular_deals'])) {
            $response['total_records'] = $popularDealCount;
            $response['total_pages'] = ceil($popularDealCount / $itemsCount);
        } else {
            $response['total_records'] = $posts->found_posts;
            $response['total_pages'] = ceil($posts->found_posts / $itemsCount);
        }



        // The Loop
        while ($posts->have_posts()) {
            $posts->the_post();
            $deal_id = get_the_ID();
            $deal_data = self::get_deal_data($deal_id);
            $deal_data['post_date'] = strtotime(get_the_date());
            $records[] = $deal_data;
         
        }

        //handle pagination for popular deals
        if (isset($request['popular_deals']) && !empty($request['popular_deals'])) {
            $response['debug'] = ['count' => $itemsCount, 'offset' => $offset];
        }

        $response['data'] = $records;

        return $response;
    }

    public function gePinnedDeals($request)
    {
        $response = ['status' => true, 'data' => [], 'message' => 'success', 'status_code' => 200];
        global $wpdb;

        $args = array(
            'post_type' => array('imdeals'),
            'post_status' => array('publish'),
            'meta_query' => [],
            'orderby' => 'post_date'
        );

        $args['meta_query'][] = [
            'key' => 'pinned_deal',
            'value' => 1,
            'compare' => '='
        ];

        $records = [];
        $posts = new WP_Query($args);

        // The Loop
        while ($posts->have_posts()) {
            $posts->the_post();

            $deal_id = get_the_ID();
            $deal_data = self::get_deal_data($deal_id);
            $deal_data['post_date'] = strtotime(get_the_date());
            $records[] = $deal_data;
        }

        $response['data'] = $records;

        return $response;
    }

    public function getDealsSuggestion($request)
    {
        $response = ['status' => true, 'data' => [], 'message' => 'success', 'status_code' => 200];

        global $wpdb;


        $limit = 5;
        $keyword = str_replace('"', '', urldecode($request['keyword']));

        $args = array(
            'post_type' => array('imdeals'),
            'post_status' => array('publish'),
            'posts_per_page' => $limit
        );

        if (!empty($keyword)) {
            $args['s'] = $keyword;
        }

        $records = [];
        $posts = new WP_Query($args);

        // The Loop
        while ($posts->have_posts()) {
            $posts->the_post();

            $deal_id = get_the_ID();
            $deal_data = self::get_deal_data($deal_id);
            $deal_data['post_date'] = strtotime(get_the_date());
            $records[] = $deal_data;
        }

        $response['data'] = $records;

        return $response;
    }


    public function getDealsFilters($request)
    {
        $response = ['status' => true, 'data' => [], 'message' => 'success', 'status_code' => 200];

        $terms = get_terms([
            'taxonomy' => 'deal_category',
            'hide_empty' => true,
        ]);

        $today = $this->getClaimedDealsCountByType('Today');
        $week = $this->getClaimedDealsCountByType('Week');
        $month = $this->getClaimedDealsCountByType('Month');
        $all = $this->getClaimedDealsCountByType('All');


        $lifeTime = $this->countDealTypes('Lifetime');
        $annual = $this->countDealTypes('Annual');
        $freeTrial = $this->countDealTypes('Free Trial');
        $featured = $this->countDealTypes('Featured');
        $expired = $this->countDealTypes('Expired');
        $endingSoon = $this->countDealTypes('Ending Soon');

        $data = [
            'categories' => $terms,
            'deal_types' => ['Lifetime', 'Annual', 'Free Trial', 'Featured', 'Expired', 'Ending Soon'],
            'deal_types_count' => ['Lifetime' => $lifeTime, 'Annual' => $annual, 'Free Trial' => $freeTrial, 'Featured' => $featured, 'Expired' => $expired, "Ending Soon" => $endingSoon],
            'most_popular_deals' => ['Today', 'Week', 'Month', 'All'],
            'most_popular_deals_count' => ['Today' => $today, 'Week' => $week, 'Month' => $month, 'All' => $all]
        ];

        $response['data'] = $data;

        return $response;
    }

    public function getTopDeals($type = 'Today')
    {
        $top_deals = [];
        global $wpdb;

        switch ($type) {
            case 'Today':
                $top_deals = $wpdb->get_col("SELECT deal_id FROM wp_deals_claimed WHERE deal_claimed_date > (now() - interval 1 DAY) group by deal_id order by sum(deal_count) DESC");
                break;
            case 'Week':
                $top_deals = $wpdb->get_col("SELECT a.deal_id FROM wp_deals_claimed a, wp_posts b WHERE a.deal_id = b.ID and b.post_status = 'publish' and YEARWEEK(a.deal_claimed_date) = YEARWEEK(NOW()) group by a.deal_id order by sum(a.deal_count) DESC");
                break;
            case 'Month':
                $top_deals = $wpdb->get_col("SELECT a.deal_id FROM wp_deals_claimed a, wp_posts b WHERE a.deal_id = b.ID and b.post_status = 'publish' and MONTH(a.deal_claimed_date) = MONTH(CURDATE()) AND YEAR(a.deal_claimed_date) = YEAR(CURDATE()) group by a.deal_id order by sum(a.deal_count) DESC");
                break;
            case 'All':
                $top_deals = $wpdb->get_col("SELECT a.deal_id FROM wp_deals_claimed a, wp_posts b WHERE a.deal_id = b.ID and b.post_status = 'publish' and b.post_type ='imdeals' group by a.deal_id order by sum(a.deal_count) DESC");
                break;
        }

        return $top_deals;
    }

    public static function get_deal_data($deal_id)
    {
        global $wpdb;
        $deal_data = array();
        $logged_userid = apply_filters('determine_current_user', false);

        $promotion_image_array = get_field('promotion_image', $deal_id);
        $deal_data['promotion_image'] = $promotion_image_array['sizes']['deals-promo'];
        $deal_data['promotion_detail_image'] = $promotion_image_array['sizes']['deals-detail'];
        $deal_data['icon'] = get_field('icon', $deal_id);
        $deal_data['offer_by'] = get_field('brands', $deal_id);
        $deal_data['recommended_deal'] = get_field('recommended_deal', $deal_id) ? 1 : 0;
        $deal_data['coupon_code'] = get_field('coupon_code', $deal_id);
        $dealTypes = ['Lifetime', 'Annual', 'Free Trial'];
        $dealVal = get_field('deal_type', $deal_id);
        $deal_data['deal_type'] = $dealVal ? $dealVal : $dealTypes[mt_rand(0, 2)];
        $deal_data['affiliate_text'] = get_field('affiliate_text', $deal_id);
        $deal_data['affiliate_link_domain_url'] = get_field('affiliate_link_domain_url', $deal_id);
        $deal_data['title'] = get_the_title($deal_id);
        $deal_data['categories'] = get_field('category', $deal_id);
        $deal_data['permalink'] = self::generate_deal_permalink($deal_id, $deal_data['categories']);
        $deal_data['short_description'] = strip_tags(get_field('short_description', $deal_id));
        $deal_data['deal_snapshot'] = get_field('deal_snapshot', $deal_id);
        $deal_data['terms_conditions'] = wpautop(get_field('terms_conditions', $deal_id));
        $deal_data['button_text'] = Im_Deals_Model::get_claim_deal_button_text($deal_data);
        $deal_data['short_coupon_code'] = Im_Deals_Model::get_short_coupon_code($deal_data);
        $deal_data['is_coupon_deal'] = Im_Deals_Model::is_coupon_deal($deal_data);
        $deal_data['deal_id'] = $deal_id;
        $deal_data['is_expired'] = get_field('deal_expired', $deal_id);
        $deal_data['deal_count'] = self::getClaimedDealsCount($deal_id, 'All');
        $deal_data['popular_deal_count'] = !empty($_GET['popular_deals']) ? self::getClaimedDealsCount($deal_id, $_GET['popular_deals']) : 0;
        $deal_data['limited_time_deal'] = get_field('limited_time_deal', $deal_id);
        $deal_data['pinned_deal'] = get_field('pinned_deal', $deal_id);

        if ($logged_userid > 0) {
            // User logged in
            $deal_data['deal_saved'] = Im_Deals_Model::is_saved_deals($logged_userid, $deal_id);
            $deal_data['logged_userid'] = $logged_userid;
        } else {
            // User not logged in
            $deal_data['deal_saved'] = '0';
            $deal_data['logged_userid'] = '0';
        }

        return $deal_data;
    }

    /**
     * Generate custom permalink to resolve random category on url
     */
    static function generate_deal_permalink($deal_id, $cats) {
        $permalinkUrl = get_permalink($deal_id);
        $permalinkRelative = str_replace(site_url().'/', '', $permalinkUrl);
        $permalinkUrlArray = explode('/', $permalinkRelative);
        if (count($permalinkUrlArray) >= 3) {
            $permalinkUrl = site_url().'/'.$permalinkUrlArray[0].'/'.$cats[0]->slug.'/'.$permalinkUrlArray[2];
        }
        return $permalinkUrl;
    }

    function edit_posts_join_paged($join_paged_statement)
    {
        //for popular deals sorting
        $join_paged_statement = "LEFT JOIN wp_deals_claimed wpdc ON wpdc.deal_id = wp_posts.ID";

        //deal type filers
        $join_paged_statement .=  " INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) ";

        //category filters
        $join_paged_statement .=  " LEFT JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id) ";

        return $join_paged_statement;
    }

    function edit_posts_orderby($orderby_statement)
    {
        global $popularFilter;

        switch ($popularFilter) {
            case 'Today':
                $orderby_statement = "(SELECT count(deal_id) from wp_deals_claimed where deal_id=wp_posts.ID AND deal_claimed_date > (now() - interval 1 DAY)) DESC";
                break;
            case 'Week':
                $orderby_statement = "(SELECT count(deal_id) from wp_deals_claimed where deal_id=wp_posts.ID AND YEARWEEK(deal_claimed_date) = YEARWEEK(NOW())) DESC";
                break;
            case 'Month':

                $orderby_statement = "(SELECT count(deal_id) from wp_deals_claimed where deal_id=wp_posts.ID AND MONTH(deal_claimed_date) = MONTH(CURDATE()) AND YEAR(deal_claimed_date) = YEAR(CURDATE())) DESC";
                break;
            default:
                $orderby_statement = "(SELECT count(deal_id) from wp_deals_claimed where deal_id=wp_posts.ID) DESC";
                break;
        }


        return $orderby_statement;
    }

    function query_group_by_filter($groupby)
    {
        return 'wp_posts.ID';
    }

    public static function getClaimedDealsCount($deal_id, $type = 'All')
    {

        $totalCount = 0;
        global $wpdb;

        switch ($type) {
            case 'Today':
                $totalCount = $wpdb->get_var("SELECT count(deal_count) FROM wp_deals_claimed WHERE deal_claimed_date > (now() - interval 1 DAY) AND deal_id = " . $deal_id . " group by deal_id order by count(deal_count) DESC");
                break;
            case 'Week':
                $totalCount = $wpdb->get_var("SELECT count(a.deal_id) FROM wp_deals_claimed a, wp_posts b WHERE a.deal_id = b.ID and b.post_status = 'publish' and YEARWEEK(a.deal_claimed_date) = YEARWEEK(NOW()) AND a.deal_id = " . $deal_id . " group by a.deal_id order by count(a.deal_count) DESC");
                break;
            case 'Month':
                $totalCount = $wpdb->get_var("SELECT count(a.deal_id) FROM wp_deals_claimed a, wp_posts b WHERE a.deal_id = b.ID and b.post_status = 'publish' and MONTH(a.deal_claimed_date) = MONTH(CURDATE()) AND YEAR(a.deal_claimed_date) = YEAR(CURDATE()) AND a.deal_id = " . $deal_id . " group by a.deal_id order by count(a.deal_count) DESC");
                break;
            case 'All':
                $totalCount = $wpdb->get_var("SELECT count(deal_id) FROM wp_deals_claimed where deal_id = " . $deal_id . " group by deal_id order by count(deal_count) DESC");
                break;
        }

        return $totalCount ? $totalCount : 0;
    }

    public static function countDealTypes($type)
    {
        $totalCount = 0;
        global $wpdb;

        switch ($type) {
            case 'Lifetime':
                $totalCount = $wpdb->get_var("SELECT count(*) FROM `wp_postmeta` WHERE meta_key like '%deal_type%' and meta_value like '%Lifetime%'");
                break;
            case 'Annual':
                $totalCount = $wpdb->get_var("SELECT count(*) FROM `wp_postmeta` WHERE meta_key like '%deal_type%' and meta_value like '%Annual%'");
                break;
            case 'Free Trial':
                $totalCount = $wpdb->get_var("SELECT count(*) FROM `wp_postmeta` WHERE meta_key like '%deal_type%' and meta_value like '%Free Trial%'");
                break;
            case 'Featured':
                $totalCount = $wpdb->get_var("SELECT count(*) FROM `wp_postmeta` WHERE meta_key like '%deal_type%' and meta_value like '%Featured%'");
                break;
            case 'Expired':
                $totalCount = $wpdb->get_var("SELECT count(*) FROM `wp_postmeta` WHERE meta_key like '%deal_expired%' and meta_value='1'");
                break;
            case 'Ending Soon':
                $totalCount = $wpdb->get_var("SELECT count(*) FROM `wp_postmeta` WHERE meta_key = 'limited_time_deal' and meta_value<>''");
                break;
        }

        return $totalCount ? $totalCount : 0;
    }


    public static function getClaimedDealsCountByType($type)
    {
        $totalCount = 0;
        global $wpdb;

        switch ($type) {
            case 'Today':
                $totalCount = $wpdb->get_var("SELECT count(deal_count) FROM wp_deals_claimed WHERE deal_claimed_date > (now() - interval 1 DAY)");
                break;
            case 'Week':
                $totalCount = $wpdb->get_var("SELECT count(a.deal_id) FROM wp_deals_claimed a, wp_posts b WHERE a.deal_id = b.ID and b.post_status = 'publish' and YEARWEEK(a.deal_claimed_date) = YEARWEEK(NOW())");
                break;
            case 'Month':
                $totalCount = $wpdb->get_var("SELECT count(a.deal_id) FROM wp_deals_claimed a, wp_posts b WHERE a.deal_id = b.ID and b.post_status = 'publish' and MONTH(a.deal_claimed_date) = MONTH(CURDATE()) AND YEAR(a.deal_claimed_date) = YEAR(CURDATE())");
                break;
            case 'All':
                $totalCount = $wpdb->get_var("SELECT count(deal_id) FROM wp_deals_claimed");
                break;
        }

        return $totalCount ? $totalCount : 0;
    }

    /**
     * To check keywork relevace
     * @param $keyword
     * @param $type
     *
     * @return boolean
     */
    public function checkKeyworyRelevance($keyword, $type = 'deal_category')
    {
        global $wpdb;
        $found = 0;

        if (strlen($keyword) > 2) {
            $check = $wpdb->get_col("SELECT t.name FROM `wp_terms` t INNER JOIN `wp_term_taxonomy` tt ON t.term_id = tt.term_id where LOWER(t.name) = '" . strtolower($keyword) . "' AND tt.taxonomy ='" . $type . "' LIMIT 1");

            $found = $check ?  $check[0] : 0;
        }

        return $found;
    }

    public function gePinnedDealIds()
    {
        global $wpdb;

        $args = array(
            'post_type' => array('imdeals'),
            'post_status' => array('publish'),
            'meta_query' => [],
            'orderby' => 'post_date'
        );

        $args['meta_query'][] = [
            'key' => 'pinned_deal',
            'value' => 1,
            'compare' => '='
        ];

        $records = [];
        $posts = new WP_Query($args);

        // The Loop
        while ($posts->have_posts()) {
            $posts->the_post();
            $records[] = get_the_ID();
        }

        return $records;
    }
}
