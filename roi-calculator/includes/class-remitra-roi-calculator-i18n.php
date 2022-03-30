<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://esipick.com/
 * @since      1.0.0
 *
 * @package    Remitra_Roi_Calculator
 * @subpackage Remitra_Roi_Calculator/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Remitra_Roi_Calculator
 * @subpackage Remitra_Roi_Calculator/includes
 * @author     Arman Sheikh <arman@team.esipick.com>
 */
class Remitra_Roi_Calculator_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'remitra-roi-calculator',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
