<?php
/**
 * Register slider post type.
 *
 * @package Lucid
 * @subpackage Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Custom post type class
if ( defined( 'LUCID_TOOLBOX_CLASS' ) && ! class_exists( 'Lucid_Post_Type' ) )
	require LUCID_TOOLBOX_CLASS . 'lucid-post-type.php';
elseif ( ! class_exists( 'Lucid_Post_Type' ) )
	return;

/*===========================================================================*\
      =Register
\*===========================================================================*/

$lucid_slider_post_type = new Lucid_Post_Type( Lucid_Slider_Core::get_post_type_name(), array(
	'small_menu_icon_url' => LUCID_SLIDER_URL . 'img/admin-icon-16.png',
	'large_menu_icon_url' => LUCID_SLIDER_URL . 'img/admin-icon-32.png',
	'post_type_args' => array(
		'hierarchical' => true,
		'labels' => array(

			// menu_name default, use plural
			'name' =>               _x( 'Sliders', 'post type general name', 'lucid-slider' ),
			'singular_name' =>      _x( 'Slider', 'post type singular name', 'lucid-slider' ),
			'all_items' =>          __( 'All sliders', 'lucid-slider' ),
			'add_new' =>            __( 'Add new', 'lucid-slider' ),
			'add_new_item' =>       __( 'Add new slider', 'lucid-slider' ),
			'edit_item' =>          __( 'Edit slider', 'lucid-slider' ),
			'new_item' =>           __( 'New slider', 'lucid-slider' ),
			'view_item' =>          __( 'View slider', 'lucid-slider' ),
			'search_items' =>       __( 'Search sliders', 'lucid-slider' ),
			'not_found' =>          __( 'No sliders found', 'lucid-slider' ),
			'not_found_in_trash' => __( 'No sliders found in trash', 'lucid-slider' ),

			// Hierarchical only
			'parent_item_colon' =>  __( 'Parent slider:', 'lucid-slider' )
		),
		'public' => false,
		'show_ui' => true,
		'capability_type' => 'page',
		'menu_position' => 25,
		'supports' => array(
			'title'
		),
		'has_archive' => false,
		'rewrite' => array( 'slug' => Lucid_Slider_Core::get_post_type_name(), 'with_front' => false ), // with_front false->/news/, true->/blog/news/
	),
	'update_messages_no_links' => array(
		'updated'   => __( 'Slider updated.', 'lucid-slider' ),
		'revision'  => __( 'Slider restored to revision from %s.', 'lucid-slider' ),
		'published' => __( 'Slider published.', 'lucid-slider' ),
		'saved'     => __( 'Slider saved.', 'lucid-slider' ),
		'submitted' => __( 'Slider submitted.', 'lucid-slider' ),
		'scheduled' => __( 'Slider scheduled for: <strong>%1$s</strong>.', 'lucid-slider' ),
		'draft'     => __( 'Slider draft updated.', 'lucid-slider' )
	)
) );