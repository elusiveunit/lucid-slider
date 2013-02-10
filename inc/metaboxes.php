<?php
/**
 * Setup metaboxes with WPAlchemy.
 *
 * @package Lucid_Slider
 * @uses WPAlchemy_MetaBox
 * @see http://www.farinspace.com/wpalchemy-metabox/
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// WPAlchemy setup
if ( ! class_exists( 'WPAlchemy_MetaBox' ) )
	require LSJL_PATH . 'classes/MetaBox.php';


/*===========================================================================*\
      =Metabox specs
\*===========================================================================*/

$lsjl_slides_meta = new WPAlchemy_MetaBox( array(
	'id' => '_lsjl-slides',
	'title' => __( 'Slides', 'lucid-slider' ),
	'template' => LSJL_PATH . 'metaboxes/slides-meta.php',
	//'mode' => WPALCHEMY_MODE_EXTRACT, // Individual wp_postmeta entries, set if using with WP_Query
	//'prefix' => 'lsjl-slider-', // A good idea with WPALCHEMY_MODE_EXTRACT
	'types' => array( Lucid_Slider_Core::get_post_type_name() ),
	'context' => 'normal', // normal, advanced, or side
	'priority' => 'default' // high, core, default or low
) );

$lsjl_slider_settings_meta = new WPAlchemy_MetaBox( array(
	'id' => '_lsjl-slider-settings',
	'title' => __( 'Slider settings', 'lucid-slider' ),
	'template' => LSJL_PATH . 'metaboxes/slider-settings-meta.php',
	//'mode' => WPALCHEMY_MODE_EXTRACT, // Individual wp_postmeta entries, set if using with WP_Query
	//'prefix' => 'lsjl-slider-', // A good idea with WPALCHEMY_MODE_EXTRACT
	'types' => array( Lucid_Slider_Core::get_post_type_name() ),
	'context' => 'side', // normal, advanced, or side
	'priority' => 'default' // high, core, default or low
) );

if ( apply_filters( 'lsjl_show_template_metabox', true ) ) :
$lsjl_slider_template_meta = new WPAlchemy_MetaBox( array(
	'id' => '_lsjl-slider-template',
	'title' => __( 'Slider template', 'lucid-slider' ),
	'template' => LSJL_PATH . 'metaboxes/slider-template-meta.php',
	//'mode' => WPALCHEMY_MODE_EXTRACT, // Individual wp_postmeta entries, set if using with WP_Query
	//'prefix' => 'lsjl-slider-', // A good idea with WPALCHEMY_MODE_EXTRACT
	'types' => array( Lucid_Slider_Core::get_post_type_name() ),
	'context' => 'normal', // normal, advanced, or side
	'priority' => 'default' // high, core, default or low
) );
endif;