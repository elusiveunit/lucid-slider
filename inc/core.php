<?php
/**
 * Core functionality and plugin setup.
 *
 * @package Lucid\Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Contains basic setup and utility functions.
 *
 * @package Lucid\Slider
 */
class Lucid_Slider_Core {

	/**
	 * Full path to plugin main file.
	 *
	 * @var string
	 */
	public static $plugin_file;

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	public static $post_type_name = 'lucidslider';

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private $_settings;

	/**
	 * Instances of some plugin classes.
	 *
	 * @var array
	 */
	private static $_instances = array();

	/**
	 * Constructor, add hooks.
	 *
	 * @param string $file Full path to plugin main file.
	 */
	public function __construct( $file ) {
		self::$plugin_file = (string) $file;

		$this->_settings = Lucid_Slider_Utility::get_settings();

		add_shortcode( 'lucidslider', array( $this, 'slider_shortcode' ) );

		add_action( 'init', array( $this, 'load_translation' ), 1 );
		add_action( 'init', array( $this, 'load_plugin_parts' ), 1 ); // Need 1 for widget
		add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
	}

	/**
	 * Load translation.
	 */
	public function load_translation() {
		load_plugin_textdomain( 'lucid-slider', false, trailingslashit( dirname( plugin_basename( self::$plugin_file ) ) ) . 'assets/lang/' );
	}

	/**
	 * Load the rest of the plugin.
	 *
	 * @global string $pagenow Current admin page.
	 */
	public function load_plugin_parts() {

		// Register custom post type
		require LUCID_SLIDER_PATH . 'inc/post-type.php';

		// Slider widget
		if ( ! empty( $this->_settings['enable_widget'] ) ) :
			require LUCID_SLIDER_PATH . 'inc/widget.php';
			$this->slider_widget();
		endif;

		// TinyMCE plugin
		if ( is_admin() && ! empty( $this->_settings['enable_tinymce'] ) ) :
			require LUCID_SLIDER_PATH . 'inc/tinymce.php';
			self::$_instances['tinymce'] = new Lucid_Slider_Tinymce();
		endif;

		// Selectively load some parts, start with admin. Ajax the WordPress way
		// goes through admin-ajax, so is_admin alone isn't enough for proper
		// admin/template separation.
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) :

			// Current admin page. Too early for exact ID via get_current_screen().
			global $pagenow;

			// Admin/dashboard related
			require LUCID_SLIDER_PATH . 'inc/admin.php';
			self::$_instances['admin'] = new Lucid_Slider_Admin();

			// Settings page
			require LUCID_SLIDER_PATH . 'inc/settings.php';

			// Edit screens. For multisite, $pagenow is null at this point.
			if ( ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) || is_null( $pagenow ) ) :

				// WPAlchemy metabox initialization
				require LUCID_SLIDER_PATH . 'inc/metaboxes.php';
				self::$_instances['metaboxes'] = new Lucid_Slider_Metaboxes();

			endif;

		// Frontend
		elseif ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) :

			// Frontend related
			require LUCID_SLIDER_PATH . 'inc/frontend.php';
			self::$_instances['frontend'] = new Lucid_Slider_Frontend();

			// Slider displaying
			require LUCID_SLIDER_PATH . 'inc/slider.php';

		endif;
	}

	/**
	 * Get the class instance with specified ID.
	 *
	 * @see load_plugin_parts() For instance IDs.
	 * @param string $id ID of instance.
	 * @return object|bool Object instance if found, false otherwise.
	 */
	public static function get_instance( $id ) {
		return ( isset( self::$_instances[$id] ) ) ? self::$_instances[$id] : false;
	}

	/**
	 * Get the slider post type name.
	 *
	 * @return string
	 */
	public static function get_post_type_name() {
		return self::$post_type_name;
	}

	/**
	 * Add image sizes.
	 *
	 * Make sure there is always a thumbnail size for the slider edit screen.
	 */
	public function add_image_sizes() {
		$opt = Lucid_Slider_Utility::get_settings();

		// Make sure post thumbnails are supported
		add_theme_support( 'post-thumbnails' );

		// Add sizes from settings
		if ( ! empty( $opt['image_sizes'] ) ) :

			// Saved as '600x200<newline>'
			$sizes = explode( "\n", trim( $opt['image_sizes'] ) );

			foreach ( $sizes as $size ) :
				$dimensions = Lucid_Slider_Utility::get_dimensions( $size );
				$size_name = Lucid_Slider_Utility::get_image_size( $size );

				if ( ! $dimensions || ! $size_name )
					continue;

				add_image_size( $size_name, $dimensions[0], $dimensions[1], true );
			endforeach;
		endif;
	}

	/**
	 * Shortcode for displaying a slider. Usage: [lucidslider id="123"]
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Slider HTML.
	 */
	public function slider_shortcode( $atts ) {
		$id = ( ! empty( $atts['id'] ) ) ? (int) $atts['id'] : 0;

		return lucid_slider_get( $id );
	}

	/**
	 * Register custom widget.
	 */
	public function slider_widget() {
		register_widget( 'Lucid_Slider_Widget' );
	}
}