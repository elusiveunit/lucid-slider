<?php
/**
 * Fired when the plugin is uninstalled. Deletes options and slider posts.
 *
 * @package Lucid
 * @subpackage Slider
 */

// Exit if the uninstall is not called from WordPress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	die();

require 'inc/core.php';

/**
 * Delete all slider posts.
 */
function lucid_slider_delete_all_posts() {
	$posts = get_posts( array(
		'numberposts' => -1,
		'post_type' => Lucid_Slider_Core::get_post_type_name(),
		'post_status' => 'any'
	) );

	if ( is_array( $posts ) ) :
		foreach ( $posts as $post )
			wp_delete_post( $post->ID, true );
	endif;
}

// Delete options
delete_option( 'lsjl_general_settings' );
delete_option( 'lsjl_slider_settings' );

// Delete slider posts
lucid_slider_delete_all_posts();