<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.appdevv.com/
 * @since             1.0.0
 * @package           Remitra_Roi_Calculator
 *
 * @wordpress-plugin
 * Plugin Name:       Remitra ROI Calculator
 * Plugin URI:        https://remitradev.wpengine.com/
 * Description:       This plugin displays range sliders with Makreto form against shortcode and calculates ROI and display to user.
 * Version:           1.0.0
 * Author:            App Dev
 * Author URI:        https://www.appdevv.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       remitra-roi-calculator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'REMITRA_ROI_CALCULATOR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-remitra-roi-calculator-activator.php
 */
function activate_remitra_roi_calculator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-remitra-roi-calculator-activator.php';
	Remitra_Roi_Calculator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-remitra-roi-calculator-deactivator.php
 */
function deactivate_remitra_roi_calculator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-remitra-roi-calculator-deactivator.php';
	Remitra_Roi_Calculator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_remitra_roi_calculator' );
register_deactivation_hook( __FILE__, 'deactivate_remitra_roi_calculator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-remitra-roi-calculator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_remitra_roi_calculator() {

	$plugin = new Remitra_Roi_Calculator();
	$plugin->run();

}
run_remitra_roi_calculator();
