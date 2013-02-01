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
		$opt = Lucid_Slider_Utility::get_settings();

		if ( ! empty( $opt['load_js'] ) )
			add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ) );

		if ( ! empty( $opt['load_css'] ) )
			add_action( 'wp_enqueue_scripts', array( $this, 'load_style' ) );
	}

	/**
	 * Register FlexSlider JavaScript.
	 *
	 * This is only registered to prevent potential double loading. The printing
	 * to page is done manually through the slider display functions. Not really
	 * the proper way, but it prevents it from being loaded on pages without a
	 * slider active.
	 */
	public function register_script() {
		wp_register_script( 'flexslider', LSJL_URL . 'js/jquery.flexslider.min.js', array( 'jquery' ), null, true );
	}

	/**
	 * Enqueue FlexSlider CSS.
	 */
	public function load_style() {
		wp_enqueue_style( 'flexslider', LSJL_URL . 'css/flexslider.min.css', false, null );
	}
}