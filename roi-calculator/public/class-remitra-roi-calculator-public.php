<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://esipick.com/
 * @since      1.0.0
 *
 * @package    Remitra_Roi_Calculator
 * @subpackage Remitra_Roi_Calculator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Remitra_Roi_Calculator
 * @subpackage Remitra_Roi_Calculator/public
 * @author     Arman Sheikh <arman@team.esipick.com>
 */
class Remitra_Roi_Calculator_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

 
	public function show_roi_provider_form() {
		global $content;
		
		ob_start();
		include( plugin_dir_path( dirname( __FILE__ ) ) .'public/partials/display-provider-roi-form.php');
		$output = ob_get_clean();
	
		return $output;
	}

	public function show_roi_supplier_form() {
		global $content;
		
		ob_start();
		include( plugin_dir_path( dirname( __FILE__ ) ) .'public/partials/display-supplier-roi-form.php');
		$output = ob_get_clean();
	
		return $output;
	}

	public function show_roi_results() {
		global $content;
		
		ob_start();
		include( plugin_dir_path( dirname( __FILE__ ) ) .'public/partials/show-roi-results.php');
		$output = ob_get_clean();
	
		return $output;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Remitra_Roi_Calculator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Remitra_Roi_Calculator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/remitra-roi-calculator-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Remitra_Roi_Calculator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Remitra_Roi_Calculator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/remitra-roi-calculator-public.js', array( 'jquery' ), $this->version, false );

	}

}
