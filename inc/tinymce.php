<?php
/**
 * TinyMCE plugin for easily inserting a shortcode.
 * 
 * @package Lucid
 * @subpackage Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Adds a button to the visual editor.
 *
 * @package Lucid
 * @subpackage Slider
 */
class Lucid_Slider_Tinymce {

	/**
	 * Constructor, add hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_editor_button' ) );
		add_action( 'wp_ajax_lucid_slider_tinymce', array( 'Lucid_Slider_Tinymce', 'tinymce_popup' ) );
	}

	/**
	 * Hook the button loading if on an edit screen and visual editing is active.
	 */
	public function add_editor_button() {
		global $pagenow;

		if ( get_user_option( 'rich_editing' )
		  && ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) ) :
			add_filter( 'mce_buttons', array( $this, 'register_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'add_button' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_css' ) );
			add_action( 'admin_footer', array( $this, 'popup_script' ) );
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
		$buttons[] = '|';
		$buttons[] = 'lucidSlider';

		return $buttons;
	}

	/**
	 * Load button script.
	 *
	 * @param array $plugins TinyMCE plugins
	 * @return array
	 */
	public function add_button( $plugins ) {
		$plugins['lucidSlider'] = LUCID_SLIDER_URL . 'js/tinymce-plugin.min.js';

		return $plugins;
	}

	/**
	 * Button and Thickbox content CSS.
	 */
	public function load_css() {
		wp_enqueue_style( 'lucid-slider-tinymce', LUCID_SLIDER_URL . 'css/tinymce-plugin.min.css', false, null );
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
	 * Script for getting the Thickbox radio value to the editor.
	 */
	public function popup_script() { ?>
		<script>
			(function($) {
				$('body').on('click', '#lsjl-tb-submit', function() {
					var $opt = $(this).prev().find('input:checked');

					if ( 1 === $opt.length ) {
						tinyMCE.activeEditor.execCommand('mceInsertContent', 0, '[lucidslider id="' + $opt.val() + '"]');
						tb_remove();
					}
				});
			})(jQuery);
		</script>
	<?php }

	/**
	 * AJAX callback for the TinyMCE Thickbox popup output.
	 */
	public static function tinymce_popup() {
		$sliders = get_posts( array(
			'post_type' => Lucid_Slider_Core::get_post_type_name(),
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		) );

		if ( ! empty( $sliders ) ) : ?>

			<div id="lsjl-tb-select">
				<h2><?php _e( 'Choose a slider', 'lucid-slider' ); ?></h2>
				<div class="lsjl-tb-labels-wrap">
				<?php foreach ( $sliders as $key => $data ) :
					$opt = get_post_meta( (int) $data->ID, '_lsjl-slider-settings', true );
					$size = ( ! empty( $opt['slider-size'] ) ) ? ucfirst( $opt['slider-size'] ) : __( 'No size', 'lucid-slider' ); ?>

					<label for="lsjl-<?php echo $data->ID; ?>">
						<span class="lsjl-tb-item-wrap">
							<input type="radio" name="lsjl-slider-id" value="<?php echo $data->ID; ?>" id="lsjl-<?php echo $data->ID; ?>">
							<?php Lucid_Slider_Admin::slide_stack( $data->ID ); ?>
							<b class="lsjl-tb-title"><?php
								printf( '%s <span>(%s)</span>', $data->post_title, $size );
							?></b>
						</span>
					</label>
				<?php endforeach; ?>
				</div>
			</div>

			<button id="lsjl-tb-submit" class="button button-primary"><?php _e( 'Insert slider', 'lucid-slider' ); ?></button>

		<?php else : ?>

			<p><?php _e( 'No sliders found', 'lucid-slider' ); ?></p>

		<?php endif;

		die();
	}
}