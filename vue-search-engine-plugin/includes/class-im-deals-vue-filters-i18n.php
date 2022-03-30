<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://google.com/
 * @since      1.0.0
 *
 * @package    Im_Deals_Vue_Filters
 * @subpackage Im_Deals_Vue_Filters/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Im_Deals_Vue_Filters
 * @subpackage Im_Deals_Vue_Filters/includes
 * @author     Zohaib Tariq <se.zohaib@gmail.com>
 */
class Im_Deals_Vue_Filters_i18n
{


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{

		load_plugin_textdomain(
			'im-deals-vue-filters',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);

	}


}
