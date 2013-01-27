<?php
/**
 * Slider displaying.
 * 
 * @package Lucid_Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Contains frontend logic related to the slider itself.
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
	public $slides;

	/**
	 * Settings for the individual slider.
	 *
	 * @var array
	 */
	public $slider_options;

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
		$this->settings = Lucid_Slider_Core::get_settings();
		$this->slider_options = get_post_meta( (int) $id, '_lsjl-slider-settings', true );

		$slides = get_post_meta( (int) $id, '_lsjl-slides', true );
		if ( ! empty( $slides['slide-group'] ) )
			$this->slides = $slides['slide-group'];
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
	 * @return string Image URL.
	 */
	protected function _get_image_src( $slide_id ) {
		$size = $this->slider_options['slider-size'];

		if ( 'full' != $size )
			$size = Lucid_Slider_Core::get_dimensions( trim( $size ) );

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

	/**
	 * Error messages when no slider is found for the supplied ID.
	 *
	 * @return string HTML content.
	 */
	protected function _no_slider_found() {
		$html = '';
		$errors = array();

		if ( empty( $this->slides ) ) $errors[] = "No slides for ID {$this->id} found.";
		if ( empty( $this->slider_options ) ) $errors[] = "Missing settings for ID {$this->id}.";

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

		if ( ! empty( $this->slides ) && ! empty( $this->slider_options ) ) :
			
			// Low priority so footer scripts are added before
			add_action( 'wp_footer', array( $this, 'slider_init' ), 999 );

			$html .= '<div class="flexslider"><ul class="slides">';

			foreach ( $this->slides as $key => $slide ) :
				$slide_id = ( ! empty( $slide['slide-image-id'] ) ) ? $slide['slide-image-id'] : 0;
				$alt = ( ! empty( $slide['slide-image-alt'] ) ) ? esc_attr( $slide['slide-image-alt'] ) : '';

				$src = $this->_get_image_src( $slide_id );
				
				// Output
				$html .= "\n<li>";
					$html = apply_filters( 'ljsl_before_slide_image', $html, $key, $slide, $this->id );

					$html .= "<img src=\"{$src}\" alt=\"{$alt}\">";

					$html = apply_filters( 'ljsl_after_slide_image', $html, $key, $slide, $this->id );
				$html .= '</li>';
			endforeach;

			$html .= "\n</ul></div>";

		else :
			$html .= $this->_no_slider_found();
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
		if ( ! self::$slider_active && ! empty( $this->settings['init_slider'] ) ) :

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
				if ( false === strpos( $setting, 's_' ) ) continue;

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
				$('.flexslider').flexslider(<?php if ( $options_added > 0 ) echo $js_options; ?>);
			})(jQuery);
			</script>
		<?php endif;
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