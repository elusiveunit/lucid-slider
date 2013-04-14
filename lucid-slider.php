<?php
/**
 * Lucid Slider definition.
 *
 * @package Lucid
 * @subpackage Slider
 */

/*
Plugin Name: Lucid Slider
Description: A simple plugin for creating Flexslider structures.
Author: Jens Lindberg
Author URI: http://profiles.wordpress.org/elusiveunit/
Version: 1.4.1
*/

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Plugin constants
if ( ! defined( 'LUCID_SLIDER_VERSION' ) )
	define( 'LUCID_SLIDER_VERSION', '1.4.1' );

if ( ! defined( 'LUCID_SLIDER_URL' ) )
	define( 'LUCID_SLIDER_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

if ( ! defined( 'LUCID_SLIDER_PATH' ) )
	define( 'LUCID_SLIDER_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

// Misc. utility functions
require LUCID_SLIDER_PATH . 'inc/utility.php';

// Load and initialize the plugin parts
require LUCID_SLIDER_PATH . 'inc/core.php';
$lucid_slider_core = new Lucid_Slider_Core( __FILE__ );