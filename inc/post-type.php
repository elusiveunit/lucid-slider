<?php
/**
 * Register slider post type.
 * 
 * @package Lucid_Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

if ( ! class_exists( 'Lucid_Slider_Post_Type' ) )
	require LSJL_PATH . 'classes/class.lucid-slider-post-type.php';

/*===========================================================================*\
      =Register
\*===========================================================================*/

$lucid_slider_post_type = new Lucid_Slider_Post_Type( 'slider', array(
	'update_message' => _x( 'Slider', 'post type singular name', 'lucid-slider' ),
	'update_message_format' => 'eng_no_links',
	'small_menu_icon_url' => LSJL_URL . 'img/admin-icon-16.png',
	'large_menu_icon_url' => LSJL_URL . 'img/admin-icon-32.png',
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
		'rewrite' => array( 'slug' => 'slider', 'with_front' => false ), // with_front false->/news/, true->/blog/news/
	)
) );