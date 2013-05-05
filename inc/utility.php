<?php
/**
 * Core functionality, always loaded.
 *
 * @package Lucid
 * @subpackage Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Contains basic setup and utility functions.
 *
 * @package Lucid
 * @subpackage Slider
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
	 * @param string $size Image size. Width and height separated with an 'x'.
	 * @return array Width as the first item, height as the second.
	 */
	public static function get_dimensions( $size ) {
		$dimensions = explode( 'x', trim( (string) $size ) );

		// Need two values
		if ( 2 != count( $dimensions ) )
			return false;

		$dimensions[0] = (int) $dimensions[0];
		$dimensions[1] = (int) $dimensions[1];

		return $dimensions;
	}

	/**
	 * Get an image size name from a string like 200x300.
	 *
	 * @param string $size Image size. Width and height separated with an 'x'.
	 * @return string Image size like 'lsjl-[size]-slide-image'.
	 */
	public static function get_image_size( $size ) {
		$size_name = preg_replace( '/[\s]+/', '', str_replace( 'x', '-', $size ) );

		return 'lsjl-' . $size_name . '-slide-image';
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
	 * <code>
	 * $user_templates['unique_template_name'] = array(
	 * 	'name' => __( 'User-visible name', 'textdomain' ),
	 * 	'path' => 'path/to/template-display-file.php',
	 * 	'screenshot' => 'URL/to/screenshot.jpg'
	 * )
	 * </code>
	 *
	 * Screenshot container is 250x100 pixels.
	 *
	 * @return array
	 */
	public static function get_templates() {
		$default_templates = array(
			'default' => array(
				'name' => __( 'Default', 'lucid-slider' ),
				'path' => LUCID_SLIDER_PATH . 'templates/default/slider.php',
				'screenshot' => LUCID_SLIDER_URL . 'templates/default/screenshot.jpg'
			)
		);
		$user_templates = apply_filters( 'lsjl_templates', array() );

		return array_merge( $default_templates, $user_templates );
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
		$image_sizes = wp_get_attachment_metadata( $slide_id );
		if ( empty( $image_sizes['sizes'] ) )
			return '';

		// If the result from get_dimensions is an array, the size is a dimension
		// string.
		$size_dim = self::get_dimensions( trim( $size ) );
		$is_dimension = ( is_array( $size_dim ) );
		if ( $is_dimension )
			$size = $size_dim;

		$use_full_size = ( 'full' == $size );

		if ( ! $use_full_size ) :

			// Assume full size and override if there is a resized image
			// available.
			$use_full_size = true;
			foreach ( $image_sizes['sizes'] as $size_name => $data ) :

				// If a dimension string is passed and width and height matches,
				// there is a crop available.
				if ( $is_dimension
				  && $size[0] == $data['width']
				  && $size[1] == $data['height'] ) :
					$use_full_size = false;

				// Otherwise check if the name exists.
				elseif ( $size_name == $size ) :
					$use_full_size = false;
				endif;
			endforeach;
		endif;

		if ( $use_full_size ) :
			$src = wp_get_attachment_url( $slide_id );
		else :
			$src = wp_get_attachment_image_src( $slide_id, $size );
			$src = $src[0];
		endif;

		return $src;
	}

	/**
	 * Shows the first three images of a slider, in a stacked style.
	 *
	 * @param int $post_id Slider post ID.
	 * @param bool $fixed_width Set a fixed width on the containing element no
	 *    matter how many images are displayed. Default true.
	 */
	public static function slide_stack( $post_id, $fixed_width = true ) {
		$slides = get_post_meta( (int) $post_id, '_lsjl-slides', true );

		if ( ! empty( $slides['slide-group'] ) ) :
			$slides = $slides['slide-group'];

			// Containing element width
			$width = 240;
			if ( ! $fixed_width ) :
				$times = 1;
				if ( 2 == count( $slides ) ) $times = 2;
				if ( 2 < count( $slides ) ) $times = 3;

				$width = 120 + ( $times * 40 );
			endif;

			$output = "<span style=\"width: {$width}px; position: relative; display: inline-block;\">";

			$count = 0;
			foreach ( $slides as $slide ) :

				// Only show the first three images
				if ( $count > 2 ) continue;

				// CSS styles. $count is 0 for first image.
				$position = ( 0 === $count ) ? 'relative' : 'absolute'; // First is relative to prevent collapsing
				$height = 80 - ( $count * 10 ); // Decrease height 10px from previous
				$top = $count * 5; // Half the height reduction
				$z_index = 5 - $count; // Stack downwards
				$left = $count * ( $height + $top - 10 ); // Arbitrary formula

				$style = "position: {$position}; width: auto; height: {$height}px; top: {$top}px; left: {$left}px; z-index: {$z_index}; border: 2px solid #fff; border-radius: 3px; box-shadow: 0 0 2px rgba(0,0,0,0.5);";

				if ( ! empty( $slide['slide-image-thumbnail'] ) ) :
					$output .= "<img src=\"{$slide['slide-image-thumbnail']}\" alt=\"\" style=\"{$style}\">";
				endif;

				$count++;
			endforeach;

			echo $output .= '</span>';
		endif;
	}
}