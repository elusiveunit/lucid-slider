<?php
/**
 * Frontend functionality.
 *
 * @package Lucid\Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Contains everything frontend related except slider displaying.
 *
 * @package Lucid\Slider
 */
class Lucid_Slider_Frontend {

	/**
	 * Constructor, add hooks.
	 *
	 * Checks the options for JavaScript/CSS loading and calls the enqueuing if
	 * they're set.
	 */
	public function __construct() {
		$opt = Lucid_Slider_Utility::get_settings();

		if ( ! empty( $opt['load_js'] ) )
			add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ) );

		if ( ! empty( $opt['load_css'] ) )
			add_action( 'wp_enqueue_scripts', array( $this, 'load_style' ) );
	}

	/**
	 * Register FlexSlider JavaScript.
	 *
	 * The enqueuing is done in the slider display function, to prevent
	 * unecessary loading.
	 */
	public function register_script() {
		wp_register_script( 'lucidslider-flexslider', LUCID_SLIDER_ASSETS . 'js/jquery.flexslider.min.js', array( 'jquery-core' ), LUCID_SLIDER_VERSION, true );
	}

	/**
	 * Enqueue FlexSlider CSS.
	 */
	public function load_style() {
		wp_enqueue_style( 'lucidslider-flexslider', LUCID_SLIDER_ASSETS . 'css/flexslider.min.css', false, LUCID_SLIDER_VERSION );
	}
}