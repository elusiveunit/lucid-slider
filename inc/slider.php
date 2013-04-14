<?php
/**
 * Slider displaying.
 * 
 * @package Lucid
 * @subpackage Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Contains frontend logic related to the slider itself.
 *
 * @package Lucid
 * @subpackage Slider
 */
class Lucid_Slider {

	/**
	 * Slider ID.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Lucid Slider settings.
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Data for every slide.
	 *
	 * @var array
	 */
	public $slides = array();

	/**
	 * Stored slide URLs to save database queries.
	 *
	 * @var array
	 */
	public $slides_urls = array();

	/**
	 * Settings for the individual slider.
	 *
	 * @var array
	 */
	public $slider_options;

	/**
	 * Template settings for the individual slider.
	 *
	 * @var array
	 */
	public $slider_template;

	/**
	 * Whether a slider has been initialized.
	 *
	 * @var boolean
	 */
	static $slider_active = false;

	/**
	 * Constructor.
	 *
	 * @param int $id Slider ID.
	 */
	public function __construct( $id = 0 ) {
		$this->id = (int) $id;
		$this->settings = Lucid_Slider_Utility::get_settings();
		$this->templates = Lucid_Slider_Utility::get_templates();

		$this->slider_options = get_post_meta( (int) $id, '_lsjl-slider-settings', true );
		$this->slider_template = get_post_meta( (int) $id, '_lsjl-slider-template', true );

		$slides = get_post_meta( (int) $id, '_lsjl-slides', true );
		if ( ! empty( $slides['slide-group'] ) )
			$this->slides = $slides['slide-group'];

		$this->slides_urls = get_post_meta( (int) $id, '_lsjl-slides-urls', true );
	}

	/**
	 * Error messages when slider files or settings are missing.
	 *
	 * @param string $template File path for invalid template.
	 * @return string HTML content.
	 */
	protected function _slider_error( $template = '' ) {
		$html = '';
		$errors = array();

		if ( empty( $this->slides ) ) $errors[] = "No slides for ID {$this->id} found.";
		if ( empty( $this->slider_options ) ) $errors[] = "Missing settings for ID {$this->id}.";
		if ( ! empty( $template ) ) $errors[] = "Invalid template: {$template} does not exist.";

		foreach ( $errors as $error ) :

			// Display clear messages when developing
			if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) :
				trigger_error( $error, E_USER_WARNING );

			// Otherwise keep errors 'developer only' in the form of comments,
			// since a slider shouldn't be critical to the page.
			else :
				$html .= "\n<!-- {$error} -->";
			endif;

		endforeach;

		return $html;
	}

	/**
	 * Create a slider structure.
	 *
	 * @return string HTML for the slider.
	 */
	public function get_slider() {
		$html = '';
		$template = ( ! empty( $this->slider_template['template'] ) )
			? $this->slider_template['template']
			: 'default';

		// First check that we have necessary settings
		if ( ! empty( $this->slides )
		  && ! empty( $this->slider_options )
		  && ! empty( $this->templates[$template] ) ) :

			$template_path = ( ! empty( $this->templates[$template]['path'] ) )
				? $this->templates[$template]['path']
				: $this->templates['default']['path'];

			// Then check if the template exists
			if ( file_exists( $template_path ) ) :

				if ( ! empty( $this->settings['load_js'] ) )
					wp_enqueue_script( 'flexslider' );

				// Low priority so footer scripts are added before
				add_action( 'wp_footer', array( $this, 'slider_init' ), 999 );

				ob_start();

				// Variables without 'this' outside the class
				$slides = $this->slides;
				$options = $this->slider_options;
				$slides_urls = $this->slides_urls;

				include $template_path;
				$html = ob_get_clean();

			// Set template does not exist
			else :
				$html .= $this->_slider_error( $template_path );
			endif;

		// Missing settings/data
		else :
			$html .= $this->_slider_error();
		endif;

		return $html;
	}

	/**
	 * Display a slider.
	 */
	public function display_slider() {
		echo $this->get_slider();
	}

	/**
	 * Initialize the slider.
	 *
	 * @see http://www.woothemes.com/flexslider/
	 */
	public function slider_init() {
		if ( self::$slider_active || empty( $this->settings['init_slider'] ) ) return;

		// Only need a single JavaScript initialization.
		self::$slider_active = true;

		// Flexslider defaults, used if the option isn't passed
		$default_options = array(
			's_animation' => 'fade',
			's_direction' => 'horizontal',
			's_animationLoop' => 1,
			's_smoothHeight' => 0,
			's_slideshow' => 1,
			's_slideshowSpeed' => 7000,
			's_animationSpeed' => 600,
			's_randomize' => 0,
			's_controlNav' => 1,
			's_directionNav' => 1,
			's_prevText' => 'Previous',
			's_nextText' => 'Next',
			's_pauseOnAction' => 1,
			's_pauseOnHover' => 1,
			's_touch' => 1,
			's_video' => 0
		);

		$js_options = '{ ';
		$options_added = 0;

		// Build JavaScript object for settings
		foreach ( $this->settings as $setting => $value ) :

			// Only care about slider options, which are prefixed with 's_'
			if ( 0 !== strpos( $setting, 's_' ) ) continue;

			// Only add option to the object if it's different from the default
			if ( $default_options[$setting] != $value ) :
				$key = str_replace( 's_', '', $setting );

				// Add comma after first option
				if ( $options_added > 0 ) $js_options .= ', ';

				// Object key
				$js_options .= $key . ': ';

				// Make the value format JavaScript friendly
				if ( 1 === $value ) :
					$js_options .= 'true';
				elseif ( 0 === $value ) :
					$js_options .= 'false';
				elseif ( is_string( $value ) ) :
					$js_options .= '"' . $value . '"';
				else :
					$js_options .= $value;
				endif;
				
				$options_added++;
			endif;
		endforeach;

		$js_options = apply_filters_ref_array( 'lsjl_js_options', array( $js_options, &$options_added ) );

		$js_options .= ' }'; ?>
		<script>
		(function($){
			$('.flexslider').flexslider(<?php if ( $options_added > 0 ) echo str_replace( "\n", '', $js_options ); ?>);
		})(jQuery);
		</script>
		<?php
	}
}

/**
 * Expose a simple global function for showing a slider.
 *
 * @param int $id ID of the slider to show.
 */
function lucid_slider( $id ) {
	$slider = new Lucid_Slider( $id );
	$slider->display_slider();
}

/**
 * Expose a simple global function for returning a slider structure.
 *
 * @param int $id ID of the slider to show.
 */
function lucid_slider_get( $id ) {
	$slider = new Lucid_Slider( $id );
	return $slider->get_slider();
}