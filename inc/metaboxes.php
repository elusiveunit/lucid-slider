<?php
/**
 * Metabox generation.
 *
 * @package Lucid\Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// WPAlchemy setup
if ( defined( 'LUCID_TOOLBOX_CLASS' ) && ! class_exists( 'WPAlchemy_MetaBox' ) )
	require LUCID_TOOLBOX_CLASS . 'MetaBox.php';
elseif ( ! class_exists( 'WPAlchemy_MetaBox' ) )
	return;

/**
 * Setup metaboxes with WPAlchemy.
 *
 * A wrapper for a wrapper... pretty silly, but reduces copy-paste when
 * creating multiple metaboxes.
 *
 * @uses WPAlchemy_MetaBox
 * @see http://www.farinspace.com/wpalchemy-metabox/
 * @package Lucid\Slider
 */
class Lucid_Slider_Metaboxes {

	/**
	 * Created metaboxes.
	 *
	 * @var array
	 */
	public $metaboxes = array();

	/**
	 * Constructor, do the registering.
	 */
	public function __construct() {
		if ( ! defined( 'LUCID_TOOLBOX_VERSION' ) ) return;

		$this->register_metabox( 'slides', array(
			'title' => __( 'Slides', 'lucid-slider' ),
			'area' => 'after_title'
		) );

		$this->register_metabox( 'slider-settings', array(
			'title' => __( 'Slider settings', 'lucid-slider' ),
		) );

		if ( apply_filters( 'lsjl_show_template_metabox', true ) ) :
			$this->register_metabox( 'slider-template', array(
				'title' => __( 'Slider template', 'lucid-slider' ),
				'context' => 'normal'
			) );
		endif;
	}

	/**
	 * Register a WPAlchemy metabox.
	 *
	 * @param string $id Metabox ID. Will be a part of the full id; prefixed
	 *    with '_lsjl-'.
	 * @param array $args Metabox arguments, will overwrite defaults.
	 */
	public function register_metabox( $id, array $args = array() ) {
		$args = array_merge( array(
			'id' => "_lsjl-{$id}",
			'title' => __( 'Extra', 'lucid-slider' ),
			'template' => LUCID_SLIDER_PATH . "metaboxes/{$id}-meta.php",
			'types' => array( Lucid_Slider_Core::get_post_type_name() ),
			'context' => 'side', // normal, advanced, or side
			'priority' => 'default' // high, core, default or low
		), $args );

		$this->metaboxes[$id] = new WPAlchemy_MetaBox( $args );
	}

	/**
	 * Get a WPAlchemy metabox object.
	 *
	 * @param string $id Metabox ID passed to register_metabox().
	 * @return object
	 */
	public function get_metabox( $id ) {
		return ( ! empty( $this->metaboxes[$id] ) ) ? $this->metaboxes[$id] : false;
	}
}