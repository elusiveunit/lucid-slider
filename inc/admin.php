<?php
/**
 * Admin functionality.
 * 
 * @package Lucid_Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Assets and UI configuration for the admin.
 */
class Lucid_Slider_Admin {

	/**
	 * Constructor, add hooks.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		//add_filter( 'gettext', array( $this, 'insert_button_text' ), 1, 3 );
		add_filter( 'manage_edit-slider_columns', array( $this, 'admin_columns' ) );
		add_action( 'manage_slider_posts_custom_column', array( $this, 'populate_columns' ), 10, 2 );
		add_action( 'admin_head-edit.php', array( $this, 'column_style' ) );
	}

	/**
	 * Load required CSS and JavaScript.
	 */
	public function load_assets() {
		$screen = get_current_screen();
		$screen_id = $screen->id;

		if ( 'slider' == $screen_id ) :
			// Metabox style
			wp_enqueue_style( 'lsjl-style', LSJL_URL . 'css/edit-slider.min.css', false, null );

			// Media upload
			/*wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'media-upload' );*/
			wp_enqueue_media();

			// Upload handling and other misc. stuff
			wp_enqueue_script( 'lsjl-script', LSJL_URL . 'js/edit-slider.min.js', array( 'jquery' ), null, true );
		endif;
	}

	/**
	 * Edit 'Insert into Post' string in media uploader.
	 *
	 * @param string $translated_text The current localized string.
	 * @param string $source_text The original string.
	 * @param string $domain Text domain.
	 * @return string
	 */
	public function insert_button_text( $translated_text, $source_text, $domain ) {
		if ( // lsjl... set in JS, to make sure the request is from Lucid Slider
		  isset( $_REQUEST['lsjl-from-slide-insert'] )
		  && 'lucid-slider' == $_REQUEST['lsjl-from-slide-insert']
		  && 'Insert into Post' == $source_text ) :
			$translated_text = __( 'Add to slide', 'lucid-slider' );
		endif;

		return $translated_text;
	}

	/**
	 * Add columns to the slider post listing.
	 *
	 * @param array $columns Default columns.
	 * @return array
	 */
	public function admin_columns( $columns ) {
		$last = array_splice( $columns, -1 );

		$columns['lsjl_images'] = __( 'Overview', 'lucid-slider' );
		$columns['lsjl_id'] = __( 'ID', 'lucid-slider' );
		$columns['lsjl_shortcode'] = __( 'Shortcode', 'lucid-slider' );

		$columns += $last;

		return $columns;
	}

	/**
	 * Populate the columns added in admin_columns().
	 *
	 * @param string $column Name of the current column.
	 * @param int $post_id Current row post ID.
	 * @see admin_columns()
	 */
	public function populate_columns( $column, $post_id ) {

		// Images column.
		if ( 'lsjl_images' == $column ) :
			Lucid_Slider_Admin::slide_stack( $post_id );

		// ID column
		elseif ( 'lsjl_id' == $column ) :
			echo $post_id;

		// Shortcode column
		elseif ( 'lsjl_shortcode' == $column ) :
			echo "<code>[lucidslider id=\"{$post_id}\"]</code>";

		endif;
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

	/**
	 * Limit the column widths.
	 */
	public function column_style() { ?>
		<style>
			.widefat .column-lsjl_images {width: 290px;}
			.widefat .column-lsjl_id {width: 5em;}
			.widefat .column-lsjl_shortcode {width: 13em;}
		</style>
	<?php }
}