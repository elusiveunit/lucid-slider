<?php
/**
 * Frontend functionality.
 * 
 * @package Lucid_Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Contains everything frontend related except slider displaying.
 */
class Lucid_Slider_Frontend {

	/**
	 * Constructor, add hooks.
	 *
	 * Checks the options for JavaScript/CSS loading and calls the enqueuing if
	 * they're set.
	 */
	public function __construct() {
		$opt = Lucid_Slider_Core::get_settings();

		if ( ! empty( $opt['load_js'] ) )
			add_action( 'wp_enqueue_scripts', array( $this, 'load_script' ) );

		if ( ! empty( $opt['load_css'] ) )
			add_action( 'wp_enqueue_scripts', array( $this, 'load_style' ) );
	}

	/**
	 * Enqueue FlexSlider JavaScript.
	 */
	public function load_script() {
		wp_enqueue_script( 'flexslider', LSJL_URL . 'js/jquery.flexslider.min.js', array( 'jquery' ), null, true );
	}

	/**
	 * Enqueue FlexSlider CSS.
	 */
	public function load_style() {
		wp_enqueue_style( 'flexslider', LSJL_URL . 'css/flexslider.min.css', false, null );
	}
}