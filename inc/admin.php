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
		add_filter( 'manage_edit-slider_columns', array( $this, 'admin_columns' ) );
		add_action( 'manage_slider_posts_custom_column', array( $this, 'populate_columns' ), 10, 2 );
		add_action( 'admin_head-edit.php', array( $this, 'column_style' ) );
		add_action( 'save_post', array( $this, 'set_slide_meta' ) );
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
			wp_enqueue_media();

			// Upload handling and other misc. stuff
			wp_enqueue_script( 'lsjl-script', LSJL_URL . 'js/edit-slider.min.js', array( 'jquery' ), null, true );
		endif;
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

	/**
	 * Save image URLs to slide post meta.
	 *
	 * The wp_get_attachment_* functions used to grab the slide image URLs does
	 * database queries for every image, which can rapidly increase queries per
	 * page request (disregarding potential caching). This instead does that
	 * task on post save for a single user and saves the URLs as post meta data.
	 *
	 * @param int $post_id Post ID.
	 */
	public function set_slide_meta( $post_id ) {

		// Make sure not to do unnecessary processing.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		  || empty( $_POST['post_type'] )
		  || 'slider' != $_POST['post_type']
		  || ! current_user_can( 'edit_post', $post_id ) ) return;

		// Get meta from POST, since the data from get_post_meta is from before
		// this save being processed.
		$slides = ( ! empty( $_POST['_lsjl-slides']['slide-group'] ) )
			? $_POST['_lsjl-slides']['slide-group']
			: false;
		$slider_size = ( ! empty( $_POST['_lsjl-slider-settings']['slider-size'] ) )
			? $_POST['_lsjl-slider-settings']['slider-size']
			: 'full';
		$slide_image_urls = array();

		// Set image URLs as id => URL
		if ( $slides ) :
			foreach ( $slides as $key => $data ) :
				if ( ! empty( $data['slide-image-id'] ) ) :
					$id = $data['slide-image-id'];

					$slide_image_urls[$id] = Lucid_Slider_Utility::get_slide_image_src( $id, $slider_size );
				endif;
			endforeach;
		endif;

		// Save meta
		update_post_meta( $post_id, '_lsjl-slides-urls', $slide_image_urls );
	}
}