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
class Lucid_Slider_Utility {

	/**
	 * Plugin settings
	 *
	 * @var array
	 */
	static $settings;

	/**
	 * Get a two-value array of image dimensions from a string like 200x300.
	 *
	 * @param string $size Sizes, separated with an 'x'.
	 * @return array Width as the first item, height as the second.
	 */
	public static function get_dimensions( $size ) {
		$dimensions = explode( 'x', trim( (string) $size ) );

		// Need two values
		if ( 2 != count( $dimensions ) ) return '';

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

		if ( empty( self::$settings ) ) :
			$general = (array) get_option( 'lsjl_general_settings' );
			$slider = (array) get_option( 'lsjl_slider_settings' );

			self::$settings = array_merge( $general, $slider );
		endif;

		return self::$settings;
	}

	/**
	 * Get all "registered" templates.
	 *
	 * Templates can be added with the lsjl_templates filter, simply by
	 * mimicking the structure of:
	 *
	 * $user_templates['unique_template_name'] = array(
	 * 	'name' => __( 'User-visible name', 'textdomain' ),
	 * 	'path' => 'path/to/template-display-file.php',
	 * 	'screenshot' => 'URL/to/screenshot.jpg'
	 * )
	 *
	 * Screenshot container is 250x100 pixels.
	 *
	 * @return array
	 */
	public static function get_templates() {
		$default_templates = array(
			'default' => array(
				'name' => __( 'Default', 'lucid-slider' ),
				'path' => LSJL_PATH . 'templates/default/slider.php',
				'screenshot' => LSJL_URL . 'templates/default/screenshot.jpg'
			)
		);
		$user_templates = apply_filters( 'lsjl_templates', array() );

		return array_merge( $default_templates, $user_templates );

		/*return apply_filters( 'lsjl_templates', array(
			'default' => array(
				'name' => __( 'Default', 'lucid-slider' ),
				'path' => LSJL_PATH . 'templates/default.php',
				'screenshot' => LSJL_PATH . 'templates/default'
			)
		) );*/
	}

	/**
	 * Get slide image URL.
	 *
	 * If the uploaded image is the exact same size as an added image size, that
	 * size is not available as an 'image size' that can be grabbed with
	 * wp_get_attachment_image_src (i.e. add_image_size with 600x200 and upload
	 * a 600x200 image). Therefore, if an intermediate image size is requested,
	 * all registered sizes are checked against the dimensions set in the slider
	 * settings. If there is a match, there is a crop, if not, the full image is
	 * assumed to be the correct one to get.
	 *
	 * @param int $slide_id Image ID.
	 * @param string $size Image size to get, i.e. '600x150' or 'full'.
	 * @return string Image URL.
	 */
	public static function get_slide_image_src( $slide_id, $size ) {

		if ( 'full' != $size )
			$size = self::get_dimensions( trim( $size ) );

		$image_sizes = wp_get_attachment_metadata( $slide_id );
		if ( empty( $image_sizes['sizes'] ) )
			return '';

		$use_full_size = ( 'full' == $size );

		if ( 'full' != $size ) :

			// Assume full size and override if there is a resized image
			// available.
			$use_full_size = true;
			foreach ( $image_sizes['sizes'] as $size_name => $data ) :

				// If width and height matches, there is a crop available.
				if ( $size[0] == $data['width'] && $size[1] == $data['height'] ) :
					$use_full_size = false;
				endif;
			endforeach;
		endif;

		if ( $use_full_size )
			$src = wp_get_attachment_url( $slide_id );
		else
			$src = wp_get_attachment_image_src( $slide_id, $size )[0];

		return $src;
	}
}