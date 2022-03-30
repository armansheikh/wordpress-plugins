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

class Im_Deals_Vue_Filter_Content
{

	/**
	 * Display Front Page Flexible Content
	 * @return string
	 */
	public static function flexible_content_vue_filters_page()
	{
		ob_start();
		require apply_filters('im_deals_vue_filters_view_template_file', 'template'); // now we use a single file
		$flexible_content_vue_filters_page = ob_get_clean();
		return $flexible_content_vue_filters_page;
	}

}
