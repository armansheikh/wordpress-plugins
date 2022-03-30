<?php

/**
 * The file that defines the IM Deals object
 *
 *
 * @link       https://wpframer.com/
 * @since      1.0.0
 *
 * @package    Im_Deals_Vue_Filters
 * @subpackage Im_Deals_Vue_Filters/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Im_Deals_Vue_Filters
 * @subpackage Im_Deals_Vue_Filters/includes
 * @author     Zohaib Tariq <se.zohaib@gmail.com>
 */
class Im_Deals_Vue_Filters_Model {

	public static $coupon_button_text = '';
	public static $non_coupon_button_text = '';

	public static function get_deal_featured_args() {
		$posts_per_page = get_sub_field('total_deals_to_display');

		$args = array(
			'post_type'      => array( 'imdeals' ),
			'post_status'    => array( 'publish' ),
			'order'          => 'DESC',
			'posts_per_page' => $posts_per_page,
			'meta_key'    => 'recommended_deal',
			'meta_value'  => '1',
		);

		return $args;
	}

	/**
	 * Check whether user has saved the deals in favorites
	 */
	public static function is_saved_deals($logged_userid, $deal_id) {
		$count_arg = array(
			'post_type' => array('saved_deals'),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'user_id',
					'value' => $logged_userid,
					'compare' => '=',
				),
				array(
					'key' => 'deals_id',
					'value' => $deal_id,
					'compare' => '=',
				)
			),
		);
		$count_deals = new WP_Query( $count_arg );

		if ( $count_deals->have_posts() ) {
			while ( $count_deals->have_posts() ) {
				$count_deals->the_post();
				$deal_saved = '1';
			}
		} else {
			$deal_saved = '0';
		}

		return $deal_saved;
	}

	/**
	 * Retreive deal data for processing and display
	 */
	public static function get_deal_data($deal_id) {
		$deal_data = array();
		$logged_userid = get_current_user_id();

		$promotion_image_array = get_field('promotion_image', $deal_id);
		$deal_data['promotion_image'] = $promotion_image_array['sizes']['deals-promo'];
		$deal_data['promotion_detail_image'] = $promotion_image_array['sizes']['deals-detail'];
		$deal_data['icon'] = get_field('icon', $deal_id);
		$deal_data['offer_by'] = get_field('brands', $deal_id);
		$deal_data['coupon_code'] = get_field('coupon_code', $deal_id);
		$deal_data['affiliate_text'] = get_field('affiliate_text', $deal_id);
		$deal_data['affiliate_link_domain_url'] = get_field('affiliate_link_domain_url', $deal_id);
		$deal_data['title'] = get_the_title($deal_id);
		$deal_data['permalink'] = get_the_permalink($deal_id);
		$deal_data['categories'] = get_field('category', $deal_id);
		$deal_data['short_description'] = get_field('short_description',$deal_id);
		$deal_data['description'] = get_field('description',$deal_id);
		$deal_data['deal_snapshot'] = get_field('deal_snapshot',$deal_id);
		$deal_data['terms_conditions'] = wpautop(get_field('terms_conditions',$deal_id));
		$deal_data['button_text'] = self::get_claim_deal_button_text($deal_data);
		$deal_data['coupon_not_working'] = self::get_coupon_not_working_link($deal_data['title']);
		$deal_data['terms_and_condition_html'] = self::get_terms_condition_link($deal_data['terms_conditions']);
		$deal_data['short_coupon_code'] = self::get_short_coupon_code($deal_data);
		$deal_data['is_coupon_deal'] = self::is_coupon_deal($deal_data);
		$deal_data['deal_id'] = $deal_id;

		if($logged_userid > 0) {
			// User logged in
			$deal_data['deal_saved'] = self::is_saved_deals($logged_userid, $deal_id);
			$deal_data['logged_userid'] = $logged_userid;
		} else {
			// User not logged in
			$deal_data['deal_saved'] = '0';
			$deal_data['logged_userid'] = '0';
		}

		$deal_data['claim_deal_button'] = self::get_claim_deal_button($deal_data);
		$deal_data['save_for_later_button'] = self::get_save_for_later_button($deal_data);

		return $deal_data;
	}

	/**
	 * Retreive deal single data for processing and display
	 */
	public static function get_single_deal_data($deal_id) {
		$deal_single_data = self::get_deal_data($deal_id);

		return $deal_single_data;
	}

	/**
	 * Retreive deal single data for processing and display
	 */
	public static function get_single_deal_widget_data($deal_id) {
		$deal_widget = self::get_deal_data($deal_id);

		return $deal_widget;
	}

	/**
	 * Retreive deal link
	 */
	public static function get_deal_link_data($deal_id) {
		$deal_data = array();

		$promotion_image_array = get_field('promotion_image', $deal_id);
		$deal_data['promotion_image'] = $promotion_image_array['sizes']['deals-promo'];
		$deal_data['icon'] = get_field('icon', $deal_id);
		$deal_data['offer_by'] = get_field('brands', $deal_id);
		$deal_data['coupon_code'] = get_field('coupon_code', $deal_id);
		$deal_data['affiliate_text'] = get_field('affiliate_text', $deal_id);
		$deal_data['title'] = get_the_title($deal_id);
		$deal_data['affiliate_link_domain_url'] = get_field('affiliate_link_domain_url', $deal_id);

		$deal_data['deal_id'] = $deal_id;

		return $deal_data;
	}

	/**
	 * Retreive Featured deal in blog post single
	 */
	public static function get_post_deal_widget_data($deal_id) {
		$deal_widget = array();

		$deal_widget = self::get_deal_data($deal_id);

		return $deal_widget;
	}

	/**
	 * Query argument to fetch Saved Deals
	 */
	public static function get_saved_deal_args() {
		$posts_per_page = '100';

		$args = array(
			'post_type'      => array( 'imdeals' ),
			'post_status'    => array( 'publish' ),
			'order'          => 'DESC',
			'posts_per_page' => $posts_per_page,
			'post__in' => self::get_user_saved_deal_ids(),
		);

		return $args;
	}

	/**
	 * Get all deal ids saved by the user
	 */
	public static function get_user_saved_deal_ids() {
		$logged_userid = get_current_user_id();
		$saved_deals_array = array();
		$counter = 0;

		$args   = array(
			'post_type'      => array( 'saved_deals' ),
			'post_status'    => array( 'publish' ),
			'meta_key' => 'user_id',
			'meta_value' => $logged_userid,
		);

		// The Query
		$saved_deals = new WP_Query( $args );

		if ( $saved_deals->have_posts() ) {
			while ( $saved_deals->have_posts() ) {
				$saved_deals->the_post();
				$saved_deals_array[$counter] = get_field('deals_id');
				$counter = $counter + 1;
			}
		} else {
			$saved_deals_array[0] = 0;
		}

		// Reset Query
		//wp_reset_postdata();

		return $saved_deals_array;
	}

	/**
	 * Get Claim deal button html
	 */
	public static function get_claim_deal_button($deal_data) {
		ob_start();
		$deal_data['button_text'] = self::get_claim_deal_button_text($deal_data);
		$claim_deal_button = ob_get_clean();
		return $claim_deal_button;
	}

	/**
	 * Get claim deal button text
	 */
	public static function get_claim_deal_button_text($deal_data) {
		self::set_button_text();

		if($deal_data['coupon_code'] != '') {
			$button_text = self::$coupon_button_text;
		} else {
			$button_text = self::$non_coupon_button_text;
		}

		return $button_text;
	}

	/**
	 * Fetch the button text from options page
	 */
	private static function set_button_text() {
		// Get deal button text. Run only once.
		if(empty(self::$coupon_button_text)) {
			$deal_button_text = get_field('deal_button_text', 'option');
			self::$coupon_button_text = $deal_button_text['coupon_button_text'];
			self::$non_coupon_button_text = $deal_button_text['non_coupon_button_text'];
		}
	}

	/**
	 * Get Coupon not working link
	 */
	public static function get_coupon_not_working_link($deal_title) {
		$link_text = '<div class="coupon-issue-link"><a href="'.home_url().'/contact-us?deal='.$deal_title.'">Coupon code not working?</a></div>';
		return $link_text;
	}

	/**
	 * Get Terms and condition link
	 */
	public static function get_terms_condition_link($terms_text) {
		if($terms_text) {
			$link_text = '<div class="terms-condition-single-deal"><span class="title">Terms and Condition</span><span class="description">'.$terms_text.'</span></div>';
		} else {
			$link_text = '';
		}

		return $link_text;
	}

	/**
	 * Get Save for later html
	 */
	public static function get_save_for_later_button($deal_data) {
		ob_start();
		$save_for_later_button = ob_get_clean();
		return $save_for_later_button;
	}

	/**
	 * Get first 4 characters of coupon code
	 */
	public static function get_short_coupon_code($deal_data) {
		if(!empty($deal_data['coupon_code'])) {
			$short_coupon_code = substr($deal_data['coupon_code'], -8);
			return strtoupper($short_coupon_code);
		}
	}

	/**
	 * Does coupon available for the deal
	 */
	public static function is_coupon_deal($deal_data) {
		$coupon_deal = false;
		if(!empty($deal_data['coupon_code'])) {
			$coupon_deal = true;
		}

		return $coupon_deal;
	}

}
