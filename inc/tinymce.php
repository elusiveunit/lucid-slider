<?php
/**
 * TinyMCE plugin for easily inserting a shortcode.
 *
 * @package Lucid\Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Adds a button to the visual editor.
 *
 * @package Lucid\Slider
 */
class Lucid_Slider_Tinymce {

	/**
	 * Constructor, add hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_editor_button' ) );
	}

	/**
	 * Hook the button loading if on an edit screen and visual editing is active.
	 *
	 * @global string $pagenow Current admin page.
	 */
	public function add_editor_button() {
		global $pagenow;

		if ( get_user_option( 'rich_editing' )
		  && ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) ) :
			add_filter( 'mce_buttons', array( $this, 'register_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'add_button' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_css' ) );
			add_action( 'admin_footer', array( $this, 'popup_content' ) );
			add_filter( 'mce_external_languages', array( $this, 'localization' ) );
		endif;
	}

	/**
	 * Add button to TinyMCE.
	 *
	 * @param array $buttons Buttons to display
	 * @return array
	 */
	public function register_button( $buttons ) {

		// Insert before the kitchen sink toggle if it exists
		if ( in_array( 'wp_adv', $buttons ) ) :
			$pos = array_search( 'wp_adv', $buttons );
			$val = array( 'lucidSlider' );
			$buttons1 = array_slice( $buttons, 0, $pos );
			$buttons1[] = 'lucidSlider';
			$buttons2 = array_slice( $buttons, $pos, null );
			$buttons = array_merge( $buttons1, $buttons2 );
		else :
			$buttons[] = 'lucidSlider';
		endif;

		return $buttons;
	}

	/**
	 * Load button script.
	 *
	 * @param array $plugins TinyMCE plugins
	 * @return array
	 */
	public function add_button( $plugins ) {
		$plugins['lucidSlider'] = LUCID_SLIDER_ASSETS . "js/tinymce-4-plugin.js";

		return $plugins;
	}

	/**
	 * Button and Thickbox content CSS.
	 */
	public function load_css() {
		wp_enqueue_style( 'lucid-slider-tinymce', LUCID_SLIDER_ASSETS . "css/tinymce-plugin.css", false, null );
	}

	/**
	 * TinyMCE button localization
	 *
	 * @param array $langs Externally loaded languages
	 * @return array
	 */
	public function localization( $langs ) {
		$langs[] = LUCID_SLIDER_PATH . 'inc/tinymce-lang.php';

		return $langs;
	}

	/**
	 * The content for the TinyMCE popup.
	 */
	public static function popup_content() {
		$sliders = get_posts( array(
			'post_type' => Lucid_Slider_Core::get_post_type_name(),
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		) );

		ob_start();

		if ( ! empty( $sliders ) ) : ?>

			<div id="lsjl-t-select">
				<?php foreach ( $sliders as $key => $data ) :
					$checked = ( 0 === $key ) ? ' checked="checked"' : ''; ?>
					<label for="lsjl-<?php echo $data->ID; ?>">
						<input type="radio" name="lsjl-slider-id" value="<?php echo $data->ID; ?>" id="lsjl-<?php echo $data->ID; ?>"<?php echo $checked; ?>>
						<span class="lsjl-t-item-wrap">
							<span><?php Lucid_Slider_Utility::slide_stack( $data->ID, 100 ); ?></span>
							<span><b class="lsjl-t-title"><?php echo $data->post_title ?></b></span>
						</span>
					</label>
				<?php endforeach; ?>
			</div>

		<?php else : ?>

			<p><?php _e( 'No sliders found', 'lucid-slider' ); ?></p>

		<?php endif;

		$html = str_replace( array( "\n", "\t" ), '', ob_get_clean() ); ?>

		<script type="text/html" id="lucid-slider-tinymce-content"><?php echo $html; ?></script>
	<?php }
}