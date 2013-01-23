<?php
/**
 * Core functionality, always loaded.
 * 
 * @package Lucid_Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Contains basic setup and utility functions.
 */
class Lucid_Slider_Core {

	/**
	 * Plugin main file
	 *
	 * @var string
	 */
	public $plugin_file;

	/**
	 * Constructor, add hooks.
	 *
	 * clean_file_name is always loaded, in case some kind of front-end
	 * uploading is at work.
	 */
	public function __construct( $file = '' ) {
		$this->plugin_file = (string) $file;
		$this->load_translation();

		add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
		add_filter( 'sanitize_file_name', array( $this, 'clean_file_name' ) );
		add_shortcode( 'lucidslider', array( $this, 'slider_shortcode' ) );
	}

	/**
	 * Make the plugin available for translation.
	 */
	public function load_translation() {
		load_plugin_textdomain( 'lucid-slider', false, trailingslashit( dirname( plugin_basename( $this->plugin_file ) ) ) . 'lang/' );
	}

	/**
	 * Add image sizes.
	 *
	 * Make sure there is always a thumbnail size for the slider edit screen.
	 */
	public function add_image_sizes() {
		$opt = Lucid_Slider_Core::get_settings();

		// Make sure post thumbnails are supported
		add_theme_support( 'post-thumbnails' );

		// Add required thumbnail
		add_image_size( 'lsjl-thumbnail', 120, 80, true );

		// Add sizes from settings
		if ( ! empty( $opt['image_sizes'] ) ) :

			// Saved as '600x200<newline>'
			$sizes = explode( "\n", trim( $opt['image_sizes'] ) );
			$count = 1;

			foreach ( $sizes as $size ) :
				$dimensions = Lucid_Slider_Core::get_dimensions( $size );

				if ( ! ( $dimensions ) ) continue;

				$size_name = preg_replace( '/[\s]/', '', str_replace( 'x', '-', $size ) );

				add_image_size( 'lsjl-' . $size_name . '-slide-image', $dimensions[0], $dimensions[1], true );
				$count++;
			endforeach;
		endif;
	}

	/**
	 * Shortcode for displaying a slider. Usage: [lucidslider id="35"]
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Slider HTML.
	 */
	public function slider_shortcode( $atts ) {
		$id = ( ! empty( $atts['id'] ) ) ? (int) $atts['id'] : 0;

		return lucid_slider_get( $id );
	}

	/**
	 * Get a two-value array of image dimensions from a string like 200x300.
	 *
	 * @param string $size Sizes, separated with an 'x'.
	 * @return array Width as the first item, height as the second.
	 */
	public static function get_dimensions( $size ) {
		$dimensions = explode( 'x', trim( (string) $size ) );

		// Need two values
		if ( 2 != count( $dimensions ) ) return false;

		$dimensions[0] = (int) $dimensions[0];
		$dimensions[1] = (int) $dimensions[1];

		return $dimensions;
	}

	/**
	 * Get all the plugin settings.
	 *
	 * If settings are changed, get_option only needs to be edited in one place.
	 * Also removes the need to remember which tab a setting is on.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$general = (array) get_option( 'lsjl_general_settings' );
		$slider = (array) get_option( 'lsjl_slider_settings' );

		$settings = array_merge( $general, $slider );

		return $settings;
	}

	/**
	 * Add extra forbidden characters to be sanitized from filenames.
	 * 
	 * This function is supposed to make filenames more browser friendly.
	 * Safari 5.1.5 on Windows is currently an offender, refusing to display
	 * images with strange characters in the name. This is a problem for
	 * attached and featured images.
	 *
	 * Additionally, strings similar to image dimensions added by WordPress are
	 * removed, so some regex functionality can be more reliable.
	 * 
	 * The sanitize_file_name filter runs just before the filename is
	 * returned, so the name has already passed the default sanitation.
	 * The sanitize_file_name_chars filter can be used to modify what
	 * special characters should be handled.
	 * 
	 * @see sanitize_file_name()
	 * @param string $filename The filename to be sanitized, with default
	 *   sanitation applied.
	 * @return string The sanitized filename.
	 */
	public function clean_file_name( $filename ) {
		$special_chars = array( 'á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ', 'å', 'ä', 'ö', 'Å', 'Ä', 'Ö' );
		$sanitized_chars = array( 'a', 'e', 'i', 'o', 'u', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'U', 'N', 'a', 'a', 'o', 'A', 'A', 'O' );
		$filename = str_replace( $special_chars, $sanitized_chars, $filename );

		/* 
		 * A strange, 'incorrect' version of 'ä' (from a Mac) slipped past the
		 * above replacing and caused problems in a project, so after converting
		 * regular verisons of characters above, we strip anything that might
		 * have passed.
		 */
		$filename = preg_replace( '/[^a-zA-Z0-9\-\_\.]/', '', $filename );

		/*
		 * Removes '-300x400' style patterns, so the regex in admin.js doesn't
		 * match any 'fake' image size string, i.e. ones not added by WordPress
		 * upload.
		 * 
		 * Without this, a user might upload an image named 'i-300x400.jpg', which
		 * is then named in the style 'i-300x400-120x80.jpg' for every image size
		 * EXCEPT the original. So when the script in admin.js tries to create
		 * the thumbnail path (<image_name>-120x80) and the user chooses the full
		 * sized image, 'i-300x400.jpg' will be replaced to 'i-120x80.jpg' when
		 * the real image is named 'i-300x400-120x80.jpg'. Since one can never
		 * know if the size part of the string is user created or generated by
		 * WordPress, this just removes anything of the like.
		 */
		$filename = preg_replace( '/(-?\d+x\d+)/', '', $filename );

		return $filename;
	}
}