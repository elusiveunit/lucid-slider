<?php
/**
 * Slider displaying.
 *
 * @package Lucid\Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Contains frontend logic related to the slider itself.
 *
 * @package Lucid\Slider
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
				$is_single_slide = ( 1 === count( $this->slides ) );
				$skip_scripts = ( $is_single_slide && ! empty( $this->settings['optimize_script_loading'] ) );

				if ( ! $skip_scripts ) :
					if ( ! empty( $this->settings['load_js'] ) )
						wp_enqueue_script( 'flexslider' );

					add_action( 'wp_footer', array( $this, 'slider_options' ), 5 );
					add_action( 'wp_footer', array( $this, 'slider_init' ), 500 );
				endif;

				ob_start();

				// Variable aliases
				$slider = $this;
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
	 * Print a global JavaScript object for the options.
	 *
	 * @link http://www.woothemes.com/flexslider/ The available options.
	 */
	public function slider_options() {
		if ( self::$slider_active || empty( $this->settings['init_slider'] ) ) return;

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
		$js_options = array();

		// Build JavaScript object for settings
		foreach ( $this->settings as $setting => $value ) :

			// Only care about slider options, which are prefixed with 's_'
			if ( 0 !== strpos( $setting, 's_' ) ) continue;

			// Only add option to the object if it's different from the default
			if ( $default_options[$setting] != $value ) :
				$key = str_replace( 's_', '', $setting );
				$js_options[$key] = $value;
			endif;
		endforeach;

		/**
		 * Filter the JavaScript options array.
		 *
		 * @param array $js_options
		 */
		$js_options = apply_filters( 'lsjl_js_options', $js_options );

		// Convert options to JavaScript object format
		$options = array();
		foreach ( $js_options as $key => $value ) :
			if ( false === $value )
				$options[] = "{$key}:0";
			elseif ( 0 === strpos( $value, 'function' ) || ! is_string( $value ) )
				$options[] = "{$key}:{$value}";
			else
				$options[] = "{$key}:'{$value}'";
		endforeach;

		$options = ( $options ) ? '{' . implode( ',', $options ) . '}' : '';

		?>
		<script>var LUCID_SLIDER_OPTIONS = <?php echo $options; ?>;</script>
		<?php
	}

	/**
	 * Initialize the slider.
	 */
	public function slider_init() {
		if ( self::$slider_active || empty( $this->settings['init_slider'] ) ) return;

		// Only need a single JavaScript initialization.
		self::$slider_active = true;

		$selector = apply_filters( 'lsjl_slider_selector', '.flexslider' );

		?>
		<script>jQuery(function($){var o='undefined'!==typeof LUCID_SLIDER_OPTIONS?LUCID_SLIDER_OPTIONS:{};$('<?php echo $selector; ?>').flexslider(o)});</script>
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