<?php

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Handles custom post types; registering, update messages etc.
 *
 * The format is very similar to the standard way of registering, with some
 * additional arguments:
 * 
 * $post_type_name = new Lucid_Slider_Post_Type( 'NAME', array(
 *  	'update_message' => 'NAME',
 * 	'update_message_format' => 'ett',
 * 	'small_menu_icon_url' => THEMENAME_PLUGIN_URL . 'img/16x40_sprite.png',
 * 	'large_menu_icon_url' => THEMENAME_PLUGIN_URL . 'img/32x32.png',
 * 	'post_type_args' => array(
 * 		[...]
 * 	)
 * ) );
 *
 * The post_type_args array contains standard register_post_type arguments, see
 * http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
 *
 * @package Lucid_Slider
 */
class Lucid_Slider_Post_Type {

	/**
	 * The post type name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Additional post type data.
	 *
	 * @var array
	 */
	public $post_type_data = array();

	/**
	 * Constructor, pass post type.
	 *
	 * Arguments through the $args array:
	 * - 'update_message' (string) Post type name to display in update messages,
	 *      for example: 'Slide image' for 'Slide image published. View slide
	 *      image'.
	 * - 'update_message_format' (string) Determines the message text format.
	 *      Possible values are 'en', 'ett' and 'eng', as well as 'en_no_links'
	 *      etc. for the same messages, but without show/preview links to the
	 *      post. These can be appropriate if the post isn't supposed to be
	 *      viewed in itself, like say a post type for image slider images.
	 *      See _update_messages() for details.
	 * - 'small_menu_icon_url' (string) Absolute url to to a 16x40 pixels sprite
	 *      image to use as admin menu icon for the post type. The hover state
	 *      should be on top of the regular state in the image.
	 * - 'large_menu_icon_url' (string) Absolute url to a 32x32 image to use as
	 *      the icon beside the heading in the post edit screen.
	 * - 'post_type_args' (array) The standard arguments for register_post_type,
	 *      like 'hierarchical', 'labels', 'supports' etc. See WordPress Codex.
	 *
	 * @param string $post_type The unique post type name. Maximum 20
	 *      characters, can not contain capital letters or spaces.
	 * @param array $args Additional post type data.
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function __construct( $post_type, array $args = array() ) {
		$this->name = (string) $post_type;
		$this->post_type_data = $args;
		$this->_add_hooks();
	}

	/**
	 * Add relevant hooks for post type functions.
	 *
	 * init: Register the post type.
	 * admin_head: Add CSS for custom menu icons.
	 * post_updated_messages: Set custom update messages.
	 */
	protected function _add_hooks() {
		add_action( 'init', array( $this, '_add_post_type' ), 0 );
		add_action( 'admin_head', array( $this, '_admin_icons' ) );
		add_action( 'post_updated_messages', array( $this, '_update_messages' ) );
	}

	/**
	 * Register the custom post type.
	 */
	public function _add_post_type() {
		register_post_type(
			$this->name,
			$this->post_type_data['post_type_args']
		);
	}

	/**
	 * Update messages for custom post types.
	 *
	 * By default, custom post types use the standard post message, i.e. 'post
	 * published' etc. This sets more appropriate messages. Requires params
	 * update_message and update_message_format to be set when constructing
	 * the post type.
	 *
	 * @param array $messages Default messages.
	 * @return array Message array with custom messages added.
	 */
	public function _update_messages( $messages ) {
		global $post;

		if ( ! isset( $this->post_type_data['update_message'] )
		  || ! isset( $this->post_type_data['update_message_format'] ) )
			return $messages;

		$format = strtolower( $this->post_type_data['update_message_format'] );
		$msg = $this->post_type_data['update_message'];

		// English
		if ( $format == 'eng' ) :
			$messages[$this->name] = array(
				0 => '', // Unused. Messages start at index 1.
				1 => sprintf( __( '%1$s updated. <a href="%2$s">View %3$s</a>', 'lucid-slider' ), $msg, esc_url( get_permalink( $post->ID ) ), strtolower( $msg ) ),
				2 => $messages['post'][2],
				3 => $messages['post'][3],
				4 => sprintf( __( '%s updated.', 'lucid-slider' ), $msg ),
				/* translators: %s: date and time of the revision */
				5 => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s', 'lucid-slider' ), $msg, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6 => sprintf( __( '%1$s published. <a href="%2$s">View %3$s</a>', 'lucid-slider' ), $msg, esc_url( get_permalink( $post->ID ) ), strtolower( $msg ) ),
				7 => sprintf( __( '%s saved.', 'lucid-slider' ), $msg ),
				8 => sprintf( __( '%1$s submitted. <a target="_blank" href="%2$s">Preview %3$s</a>', 'lucid-slider' ), $msg, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ), strtolower( $msg ) ),
				9 => sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %4$s</a>', 'lucid-slider' ), $msg,
					/* translators: Publish box date format, see http://php.net/date */
					date_i18n( __( 'M j, Y @ G:i', 'lucid-slider' ), strtotime( $post->post_date ) ),
					esc_url( get_permalink( $post->ID ) ), strtolower( $msg ) ),
				10 => sprintf( __( '%1$s draft updated. <a target="_blank" href="%2$s">Preview %3$s</a>', 'lucid-slider' ), $msg, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ), strtolower( $msg ) ),
			);

		// English without links
		elseif ( $format == 'eng_no_links' ) :
			$messages[$this->name] = array(
				0 => '', // Unused. Messages start at index 1.
				1 => sprintf( __( '%s updated.', 'lucid-slider' ), $msg ),
				2 => $messages['post'][2],
				3 => $messages['post'][3],
				4 => sprintf( __( '%s updated.', 'lucid-slider' ), $msg ),
				/* translators: %s: date and time of the revision */
				5 => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s', 'lucid-slider' ), $msg, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6 => sprintf( __( '%s published.', 'lucid-slider' ), $msg ),
				7 => sprintf( __( '%s saved.', 'lucid-slider' ), $msg ),
				8 => sprintf( __( '%s submitted.', 'lucid-slider' ), $msg ),
				9 => sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>.', 'lucid-slider' ), $msg,
					/* translators: Publish box date format, see http://php.net/date */
					date_i18n( __( 'M j, Y @ G:i', 'lucid-slider' ) ) ),
				10 => sprintf( __( '%s draft updated.', 'lucid-slider' ), strtolower( $msg ) )
			);

		endif;

		return $messages;
	}

	/**
	 * Custom icons for custom post types.
	 *
	 * The small image should be a 16x40 pixels sprite image, with the hover
	 * state on top of the regular state. The large icon should be 32x32 pixels.
	 */
	public function _admin_icons() {
		$post_type = $this->name;

		$small_icon = ( isset( $this->post_type_data['small_menu_icon_url'] ) )
			? $this->post_type_data['small_menu_icon_url']
			: '';

		$large_icon = ( isset( $this->post_type_data['large_menu_icon_url'] ) )
			? $this->post_type_data['large_menu_icon_url']
			: '';

		$css = '';

		// Small icon CSS
		if ( ! empty( $small_icon ) ) :
			$css .= "#menu-posts-{$post_type} .wp-menu-image {
				background: url('{$small_icon}') no-repeat 6px -17px !important;
			}
			#menu-posts-{$post_type}:hover .wp-menu-image,
			#menu-posts-{$post_type}.wp-has-current-submenu .wp-menu-image {
				background-position: 6px 7px !important;
			}";
		endif;

		// Large icon CSS
		if ( ! empty( $large_icon ) ) :
			$css .= ".icon32-posts-{$post_type} {
				background: url('{$large_icon}') no-repeat !important;
			}";
		endif;

		// Don't print an empty style tag
		if ( empty( $css ) ) return;
		
		// Remove newlines and tabs
		$output = str_replace( "\n", '', $css );
		$output = str_replace( "\t", '', $output );

		echo "<style>{$output}</style>\n";
	}
}