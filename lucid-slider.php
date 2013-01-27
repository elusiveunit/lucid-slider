<?php
/**
 * Main file, loads relevant parts of the plugin depending on context.
 * 
 * @package Lucid_Slider
 */

/*
Plugin Name: Lucid Slider
Description: A simple plugin for creating Flexslider structures.
Author: Jens Lindberg
Author URI: http://example.com
Version: 1.2
*/

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Define plugin constants
if ( ! defined( 'LSJL_VERSION' ) )
	define( 'LSJL_VERSION', '1.2' );

if ( ! defined( 'LSJL_URL' ) )
	define( 'LSJL_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

if ( ! defined( 'LSJL_PATH' ) )
	define( 'LSJL_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/*===========================================================================*\
      =Load functionality
\*===========================================================================*/

// Setup and misc. utility functions
require 'inc/core.php';
$lucid_slider_core = new Lucid_Slider_Core( __FILE__ );

// Register custom post type
require 'inc/post-type.php';

// Another global unfortunately needed for conditional widget loading
$lucid_slider_setting = Lucid_Slider_Core::get_settings();

// Slider widget
if ( ! empty( $lucid_slider_setting['enable_widget'] ) ) :
	require 'inc/widget.php';
endif;

// Use selective loading for some parts
if ( is_admin() ) :

	// Current admin page. Too early for exact ID via get_current_screen().
	global $pagenow;

	// Admin/dashboard related
	require 'inc/admin.php';
	$lucid_slider_admin = new Lucid_Slider_Admin();

	// Settings page
	require 'inc/settings.php';

	// Edit screens
	if ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) :

		// WPAlchemy metabox initialization
		require 'inc/metaboxes.php';

	endif;

	// TinyMCE plugin
	if ( ! empty( $lucid_slider_setting['enable_tinymce'] ) ) :
		require 'tinymce/tinymce.php';
		$lucid_slider_tinymce = new Lucid_Slider_Tinymce();
	endif;

else :

	// Frontend related
	require 'inc/frontend.php';
	$lucid_slider_frontend = new Lucid_Slider_Frontend();

	// Slider displaying
	require 'inc/slider.php';

endif;