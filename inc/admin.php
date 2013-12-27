<?php
/**
 * Admin functionality.
 *
 * @package Lucid\Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Assets and UI configuration for the admin.
 *
 * @package Lucid\Slider
 */
class Lucid_Slider_Admin {

	/**
	 * Constructor, add hooks.
	 *
	 * @global string $pagenow Current admin page.
	 */
	public function __construct() {
		global $pagenow;
		$basename = plugin_basename( Lucid_Slider_Core::$plugin_file );
		$slider_post_type = Lucid_Slider_Core::get_post_type_name();
		$current_post_type = ( ! empty( $_GET['post_type'] ) ) ? $_GET['post_type'] : '';

		add_action( 'admin_notices', array( $this, 'toolbox_notice' ) );
		add_action( 'save_post', array( $this, 'set_slide_meta' ) );

		// Plugins page
		if ( 'plugins.php' == $pagenow ) :
			add_filter( "plugin_action_links_{$basename}", array( $this, 'add_action_links' ) );
			add_filter( 'plugin_row_meta', array( $this, 'add_meta_links' ), 10, 2 );
		endif;

		// Post listing page
		if ( 'edit.php' == $pagenow && $current_post_type == $slider_post_type ) :
			add_filter( "manage_edit-{$slider_post_type}_columns", array( $this, 'admin_columns' ) );
			add_action( "manage_{$slider_post_type}_posts_custom_column", array( $this, 'populate_columns' ), 10, 2 );
			add_action( 'admin_head', array( $this, 'column_style' ) );
		endif;

		// Edit post page
		if ( 'post.php' == $pagenow || 'post-new.php' == $pagenow )
			add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	/**
	 * Show a notice if Lucid Toolbox isn't activated.
	 *
	 * @global string $pagenow Current admin page.
	 */
	public function toolbox_notice() {
		global $pagenow;

		if ( 'plugins.php' == $pagenow ) :
			$active = (array) get_option( 'active_plugins' );
			$toolbox_active = false;

			// Don't check exact basename with is_plugin_active, since the folder
			// name may vary.
			foreach ( $active as $plugin ) :
				if ( false !== strpos( $plugin, 'lucid-toolbox.php' ) )
					$toolbox_active = true;
			endforeach;

			if ( ! $toolbox_active )
				printf( '<div class="error"><p>%s</p></div>', __( 'Lucid Toolbox is needed for Lucid Slider to function properly.', 'lucid-slider' ) );
		endif;
	}

	/**
	 * Add a settings page link to the plugin action links.
	 *
	 * @param array $links Default meta links.
	 * @return array
	 */
	public function add_action_links( $links ) {

		// Only add link if user have access to the page
		if ( current_user_can( 'manage_options' ) ) :
			$url = esc_attr( trailingslashit( get_admin_url() ) . 'options-general.php?page=lsjl_settings' );

			// Generally bad practice to rely on core strings, but I feel it's
			// unlikely this is ever untranslated. If it happens, it's a simple
			// update.
			$text = __( 'Settings' );

			$links['settings'] = "<a href=\"{$url}\">{$text}</a>";
		endif;

		return $links;
	}

	/**
	 * Add a documentation link to the plugin meta data.
	 *
	 * @param array $links Default meta links.
	 * @param string $basename Basename of plugin currently processing.
	 * @return array
	 */
	public function add_meta_links( $links, $basename ) {
		if ( plugin_basename( Lucid_Slider_Core::$plugin_file ) == $basename ) :
			$url = esc_attr( LUCID_SLIDER_URL . 'doc' );

			// Generally bad practice to rely on core strings, but I feel it's
			// unlikely this is ever untranslated. If it happens, it's a simple
			// update.
			$text = __( 'Documentation' );

			$links['documentation'] = "<a href=\"{$url}\">{$text}</a>";
		endif;

		return $links;
	}

	/**
	 * Load required CSS and JavaScript.
	 */
	public function load_assets() {
		$screen = get_current_screen();

		if ( $screen->id == Lucid_Slider_Core::get_post_type_name() ) :
			$style = ( version_compare( $GLOBALS['wp_version'], '3.8-alpha', '>' ) ) ? 'edit-slider-new' : 'edit-slider';

			// Metabox style
			wp_enqueue_style( 'lsjl-edit-slider', LUCID_SLIDER_ASSETS . "css/{$style}.css", false, LUCID_SLIDER_VERSION );

			// Media upload
			wp_enqueue_media();

			// Upload handling and other misc. stuff
			wp_enqueue_script( 'lsjl-edit-slider', LUCID_SLIDER_ASSETS . 'js/edit-slider.min.js', array( 'jquery-core' ), LUCID_SLIDER_VERSION, true );
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
			Lucid_Slider_Utility::slide_stack( $post_id );

		// ID column
		elseif ( 'lsjl_id' == $column ) :
			echo $post_id;

		// Shortcode column
		elseif ( 'lsjl_shortcode' == $column ) :
			echo "<code>[lucidslider id=\"{$post_id}\"]</code>";

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
		  || $_POST['post_type'] != Lucid_Slider_Core::get_post_type_name()
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