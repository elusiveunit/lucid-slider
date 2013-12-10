<?php
/**
 * Lucid Slider plugin definition.
 *
 * Plugin Name: Lucid Slider
 * Plugin URI: https://github.com/elusiveunit/lucid-slider
 * Description: A simple but extendable slider plugin.
 * Author: Jens Lindberg
 * Version: 1.6.0
 * License: GPL-2.0+
 * Text Domain: lucid-slider
 * Domain Path: /assets/lang
 *
 * @package Lucid
 * @subpackage Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Symlink workaround, see http://core.trac.wordpress.org/ticket/16953
// The root check is to stop a fatal error on activation
$lucid_slider_plugin_file = __FILE__;
$lucid_slider_document_root = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT'] );
if ( isset( $plugin ) && false !== strpos( $plugin, $lucid_slider_document_root ) )
	$lucid_slider_plugin_file = $plugin;
elseif ( isset( $network_plugin ) && false !== strpos( $network_plugin, $lucid_slider_document_root ) )
	$lucid_slider_plugin_file = $network_plugin;

// Plugin constants
if ( ! defined( 'LUCID_SLIDER_VERSION' ) )
	define( 'LUCID_SLIDER_VERSION', '1.6.0' );

if ( ! defined( 'LUCID_SLIDER_URL' ) )
	define( 'LUCID_SLIDER_URL', trailingslashit( plugin_dir_url( $lucid_slider_plugin_file ) ) );

if ( ! defined( 'LUCID_SLIDER_ASSETS' ) )
	define( 'LUCID_SLIDER_ASSETS', LUCID_SLIDER_URL . 'assets/' );

if ( ! defined( 'LUCID_SLIDER_PATH' ) )
	define( 'LUCID_SLIDER_PATH', trailingslashit( plugin_dir_path( $lucid_slider_plugin_file ) ) );

// Misc. utility functions
require LUCID_SLIDER_PATH . 'inc/utility.php';

// Load and initialize the plugin parts
require LUCID_SLIDER_PATH . 'inc/core.php';
$lucid_slider_core = new Lucid_Slider_Core( $lucid_slider_plugin_file );