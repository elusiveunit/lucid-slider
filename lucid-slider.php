<?php
/**
 * Lucid Slider plugin definition.
 *
 * Plugin Name: Lucid Slider
 * Plugin URI: https://github.com/elusiveunit/lucid-slider
 * Description: A simple but extendable slider plugin.
 * Author: Jens Lindberg
 * Version: 1.7.1
 * License: GPL-2.0+
 * Text Domain: lucid-slider
 * Domain Path: /assets/lang
 *
 * @package Lucid\Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Plugin constants
if ( ! defined( 'LUCID_SLIDER_VERSION' ) )
	define( 'LUCID_SLIDER_VERSION', '1.7.1' );

if ( ! defined( 'LUCID_SLIDER_URL' ) )
	define( 'LUCID_SLIDER_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

if ( ! defined( 'LUCID_SLIDER_ASSETS' ) )
	define( 'LUCID_SLIDER_ASSETS', LUCID_SLIDER_URL . 'assets/' );

if ( ! defined( 'LUCID_SLIDER_PATH' ) )
	define( 'LUCID_SLIDER_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

// Misc. utility functions
require LUCID_SLIDER_PATH . 'inc/utility.php';

// Load and initialize the plugin parts
require LUCID_SLIDER_PATH . 'inc/core.php';
$lucid_slider_core = new Lucid_Slider_Core( __FILE__ );