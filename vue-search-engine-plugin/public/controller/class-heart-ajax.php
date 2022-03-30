<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://google.com/
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
class Heart_Ajax
{

	/**
	 * IM Deals Ajax Callback. All Ajax request will reach this method.
	 */
	public static function heart_ajax_callback()
	{

		$task = $_POST['task'];

		switch ($task) {
			case "save_deal":
				wp_send_json(self::save_deal());
				break;
			default:
				wp_send_json("Error!");
		}

		wp_die();
	}

	/**
	 * Get Deals Count via Ajax Callback
	 */
	public static function get_deal_statistics()
	{

		// Get total deals count
		$total_deals = self::get_total_deals();

		// Get total deals claimed
		$total_claimed = self::get_total_deals_claimed();

		// Get total deals claimed today
		$total_claimed_today = self::get_total_deals_claimed_today();

		$deal_obj = new \stdClass();
		$deal_obj->total_deals = self::get_formatted_numbers($total_deals);
		$deal_obj->total_claimed = self::get_formatted_numbers($total_claimed);
		$deal_obj->total_claimed_today = self::get_formatted_numbers($total_claimed_today);

		$result = json_encode($deal_obj);

		wp_send_json($result);

		wp_die();
	}

	/**
	 * Get Total Deals Claimed via Ajax Callback
	 */
	public static function get_total_deals_claimed_count()
	{
		$total_deals_claimed = self::get_total_deals_claimed();

		$deal_obj = new \stdClass();
		$deal_obj->total_deals_claimed = self::get_formatted_numbers($total_deals_claimed);

		$result = json_encode($deal_obj);

		wp_send_json($result);

		wp_die();
	}

	/**
	 * Save Deals via Ajax Callback
	 */
	public static function save_deal()
	{

		$dealid = sanitize_text_field($_POST['deal_id']);
		$deal_status = sanitize_text_field($_POST['deal_status']);
		$userid = get_current_user_id();

//		$deal_obj = new \stdClass();
		$deal_response = [];
		$deal_response['deal_id'] = $dealid;
		$deal_response['user_id'] = $userid;

		//Im_Deals_Common::write_log($dealid . ' / ' . $deal_status . ' / ' . $userid);

		if ($deal_status == 'save') {
			// Save deals
			$post_id = wp_insert_post(
				array(
					'post_status' => 'publish',
					'post_type' => 'saved_deals'
				)
			);

			if ($post_id) {
				update_field('user_id', $userid, $post_id);
				update_field('deals_id', $dealid, $post_id);
			}

//			$deal_obj->deal_save_status = 'Deal Saved!';
			$deal_response['deal_save_status'] = 'Deal Saved!';
			wp_send_json(json_encode($deal_response));

		} else {
			$delete_deal_arg = array(
				'post_type' => array('saved_deals'),
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'user_id',
						'value' => $userid,
						'compare' => '=',
					),
					array(
						'key' => 'deals_id',
						'value' => $dealid,
						'compare' => '=',
					)
				),
			);
			$delete_deal = new WP_Query($delete_deal_arg);

			if ($delete_deal->have_posts()) {
				while ($delete_deal->have_posts()) {
					$delete_deal->the_post();
					wp_delete_post(get_the_ID());
				}
			}
			$deal_response['deal_save_status'] = 'Deal Unsaved!';
			wp_send_json(json_encode($deal_response));
		}
		wp_die();
	}

	/**
	 * Get total deals claimed
	 */
	public static function get_total_deals_claimed()
	{
		global $wpdb;

		// Get total deals claimed
		$total_claimed = $wpdb->get_var("select sum(c.count) as count from (select a.deal_id, sum(a.deal_count) as count from wp_deals_claimed a, wp_posts b where a.deal_id = b.ID and b.post_status = 'publish' group by a.deal_id order by sum(a.deal_count) desc) c ");

		return $total_claimed;
	}

	/**
	 * Get total deals claimed today
	 */
	public static function get_total_deals_claimed_today()
	{
		global $wpdb;

		// Get total deals claimed today
		$total_claimed_today = $wpdb->get_var("select sum(c.count) as count from (select a.deal_id, sum(a.deal_count) as count from wp_deals_claimed a, wp_posts b where a.deal_id = b.ID and b.post_status = 'publish' and a.deal_claimed_date > (now() - interval 1 DAY) group by a.deal_id order by sum(a.deal_count) desc) c ");

		return $total_claimed_today;
	}

	/**
	 * Get Total Deals Counts
	 */
	public static function get_total_deals()
	{
		// Get total deals
		$imdeals = wp_count_posts($post_type = 'imdeals');
		if ($imdeals) {
			$total_deals = $imdeals->publish;
		}

		return $total_deals;
	}

	/**
	 * Get Total Deals Count by Deal ID
	 */
	public static function get_deal_inner_claimed_count()
	{
		global $wpdb;
		$deal_obj = new \stdClass();

		$postid = sanitize_text_field($_POST['postid']);
		$total_claimed = $wpdb->get_var("SELECT count(deal_count) FROM wp_deals_claimed where deal_id =" . $postid);

		$deal_obj->total_claimed = $total_claimed;
		$result = json_encode($deal_obj);
		wp_send_json($result);

		wp_die();
	}

	/**
	 * This method formats numbers
	 */
	public static function get_formatted_numbers($format_var)
	{
		if ($format_var !== '') {
			$format_var_formatted = number_format($format_var);
		} else {
			$format_var_formatted = 0;
		}
		return $format_var_formatted;
	}

	/**
	 * Save user profile from Edit Profile page
	 */
	public static function save_user_profile()
	{
		$user_obj = new \stdClass();

		$profile_name = sanitize_text_field($_POST['profile_name']);
		$profile_email_address = sanitize_text_field($_POST['profile_email_address']);
		$profile_password = sanitize_text_field($_POST['profile_password']);
		$profile_photo = $_FILES['user_profile_photo'];

		// Check valid email address
		if (!is_email($profile_email_address)) {
			$user_obj->message = 'Invalid Email Address';
			$result = json_encode($user_obj);
			wp_send_json($result);
			wp_die();
		}

		// These files need to be included as dependencies when on the front end.
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');

		$userid = get_current_user_id();

		if (!empty($profile_photo['name'])) {
			// Let WordPress handle the upload.
			$img_id = media_handle_upload('user_profile_photo', 0);

			// Update profile photo
			update_user_meta($userid, 'user_profile_photo', $img_id);
		}

		$userdata = array(
			'ID' => $userid,
			'user_login' => $profile_email_address,
			'user_email' => $profile_email_address,
			'user_pass' => $profile_password,
			'first_name' => $profile_name
		);

		$user_data = wp_update_user($userdata);

		if (is_wp_error($user_data)) {
			$user_obj->message = 'Error Updating Profile!';
			$result = json_encode($user_obj);
			wp_send_json($result);
			wp_die();
		} else {
			$user_obj->message = 'Profile Updated Successfully!';
			$result = json_encode($user_obj);
			wp_send_json($result);
			wp_die();
		}
	}
}
