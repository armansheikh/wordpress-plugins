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
class Im_Deals_Vue_Filters
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Im_Deals_Vue_Filters_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('IM_DEALS_VUE_FILTERS_VERSION')) {
			$this->version = IM_DEALS_VUE_FILTERS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'im-deals-vue-filters';

		$this->load_dependencies();
		$this->set_locale();
		// $this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Im_Deals_Vue_Filters_Loader. Orchestrates the hooks of the plugin.
	 * - Im_Deals_Vue_Filters_i18n. Defines internationalization functionality.
	 * - Im_Deals_Vue_Filters_Admin. Defines all hooks for the admin area.
	 * - Im_Deals_Vue_Filters_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-im-deals-vue-filters-loader.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-im-deals-vue-filters-i18n.php';

//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-im-deals-vue-filters-admin.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-im-deals-vue-filters-public.php';


		require_once plugin_dir_path(dirname(__FILE__)) . 'public/controller/class-heart-ajax.php';
//
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/class-im-deals-user-management.php';
//
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/class-im-deals-common.php';
//
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/class-im-deals-flexible-content.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/controller/class-im-deals-vue-filter-content.php';
//
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/class-im-deals-submission.php';
//
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/class-im-deals-widgets.php';
//
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/class-im-deals-image-generation.php';
//
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/class-im-deals-by-brands.php';
//
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lib/MailChimp.php';

//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/model/class-im-deals-model.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/model/class-im-deals-vue-filters-model.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-im-deals-vue-filters-apis.php';

		$this->loader = new Im_Deals_Vue_Filters_Loader();

		//register api actions
		$filterApisObj = new Im_Deals_Vue_Filters_Api();
		$filterApisObj->registerApiRoutes();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Im_Deals_Vue_Filters_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Im_Deals_Vue_Filters_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Im_Deals_Vue_Fiters_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('init', $plugin_public, 'register_shortcodes');
		$this->loader->add_action('wp_head', $plugin_public, 'set_ajax_url');
		$this->loader->add_filter('im_deals_vue_filters_view_template', $plugin_public, 'get_vue_filters_view_template', 10, 1);
		$this->loader->add_filter('im_deals_vue_filters_view_template_file', $plugin_public, 'get_vue_filters_view_template_file', 10, 1);
		$this->loader->add_filter('body_class', $plugin_public, 'add_class_to_body');
		$this->loader->add_action('wp_ajax_heart_ajax_callback', 'Heart_Ajax', 'heart_ajax_callback');
		$this->loader->add_action('wp_ajax_nopriv_heart_ajax_callback', 'Heart_Ajax', 'heart_ajax_callback');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Im_Deals_Vue_Filters_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version()
	{
		return $this->version;
	}

}
