<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://google.com/
 * @since      1.0.0
 *
 * @package    Im_Deals_Vue_Filters
 * @subpackage Im_Deals_Vue_Filters/includes
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Im_Deals_Vue_Filters
 * @subpackage Im_Deals_Vue_Filters/includes
 * @author     Zohaib Tariq <se.zohaib@gmail.com>
 */
class Im_Deals_Vue_Fiters_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Im_Deals_Vue_Filters_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Im_Deals_Vue_Filters_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__) . 'css/fontawesome/css/all.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/im-deals-vue-filters-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Im_Deals_Vue_Filters_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Im_Deals_Vue_Filters_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/im-deals-vue-filters-public.js', array('jquery'), $this->version, true);
	}

	/**
	 * Get custom templates
	 */
	function get_custom_post_type_template($template)
	{
		global $post;
		if (is_tax('deal_category'))
			return apply_filters('im_deals_theme_view_template', 'taxonomy-deal_category');
		if (is_tax('brands'))
			return apply_filters('im_deals_theme_view_template', 'taxonomy-brands');
		if (is_post_type_archive('imdeals'))
			return apply_filters('im_deals_theme_view_template', 'archive-imdeals');
		if (is_singular('imdeals'))
			return apply_filters('im_deals_theme_view_template', 'single-imdeals');
		return $template;
	}

	/**
	 * Register shortcodes
	 */
	public function register_shortcodes()
	{
		add_shortcode('filter_with_vuejs_layout', array('Im_Deals_Vue_Filter_Content', 'flexible_content_vue_filters_page'));
		add_shortcode('display_newsletter', array('Im_Deals_Common', 'display_newsletter'));
	}

	/**
	 * Remove top admin for Subscribers
	 */
	function remove_admin_bar()
	{
		if (!current_user_can('administrator') && !is_admin()) {
			show_admin_bar(false);
		}
	}

	/**
	 * GeneratePress column width
	 */
	function generate_footer_widget_1_width()
	{
		return '50';
	}

	/**
	 * GeneratePress column width
	 */
	function generate_footer_widget_2_width()
	{
		return '15';
	}

	/**
	 * GeneratePress column width
	 */
	function generate_footer_widget_3_width()
	{
		return '15';
	}

	/**
	 * GeneratePress column width
	 */
	function generate_footer_widget_4_width()
	{
		return '20';
	}

	/**
	 * Set Ajax URL for front end use
	 */
	function set_ajax_url()
	{
		echo '<script>var ajax_url = "' . home_url() . '/wp-admin/admin-ajax.php";</script>';
	}

	/**
	 * Get view template from public/view folder
	 */
	public function get_view_template($template_name)
	{
		return plugin_dir_path(dirname(__FILE__)) . 'public/view/' . $template_name . '.php';
	}

	/**
	 * Get view template from public/view folder
	 */
	public function get_vue_filters_view_template($template_name)
	{
		return plugin_dir_path(dirname(__FILE__)) . 'public/view/flexible-layouts/' . $template_name . '.php';
	}

	/**
	 * Get view template from public/view folder
	 */
	public function get_vue_filters_view_template_file($template_name)
	{
		return plugin_dir_path(dirname(__FILE__)) . 'public/view/' . $template_name . '.php';
	}

	/**
	 * Add custom class to body
	 */
	public function add_class_to_body($classes)
	{
		global $post;
		if (isset($post->post_title))
			return array_merge($classes, array('im-deals-vue-filters-css'));
		return $classes;
	}

	/**
	 * Get view template from public/view folder
	 */
	public function get_theme_view_template($template_name)
	{
		return plugin_dir_path(dirname(__FILE__)) . 'public/view/theme-templates/' . $template_name . '.php';
	}

}
