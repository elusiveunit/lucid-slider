<?php
/**
 * Register settings page
 *
 * @package Lucid
 * @subpackage Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Settings class
if ( defined( 'LUCID_TOOLBOX_CLASS' ) && ! class_exists( 'Lucid_Settings' ) )
	require LUCID_TOOLBOX_CLASS . 'lucid-settings.php';
elseif ( ! class_exists( 'Lucid_Settings' ) )
	return;

$lsjl_settings = new Lucid_Settings( 'lsjl_settings', __( 'Lucid Slider Settings', 'lucid-slider' ) );

$lsjl_settings->submenu( 'Lucid Slider', array(
	'title' => __( 'Lucid Slider Settings', 'lucid-slider' ),

	/**
	 * Filter settings tabs.
	 *
	 * @param array
	 */
	'tabs' => apply_filters( 'lsjl_settings_tabs', array(
		'lsjl_general_settings' => _x( 'General', 'Settings tab', 'lucid-slider' ),
		'lsjl_slider_settings' => _x( 'Slider', 'Settings tab', 'lucid-slider' )
	) )
) );

// Only add fields if on the page
if ( $lsjl_settings->is_on_settings_page() ) :

	/*========================================================================*\
	      =General
	\*========================================================================*/

	/* -Images
	--------------------------------------------------------------------------*/
	$lsjl_settings->section( 'lsjl_image_section', array(
		'heading' => __( 'Images', 'lucid-slider' ),
		'tab' => 'lsjl_general_settings'
	) );

	$lsjl_settings->field(
		'image_sizes',
		__( 'Image sizes', 'lucid-slider' ),
		array(
			'type' => 'textarea_monospace',
			'must_not_match' => '/[^0-9x\s]/',
			'error_message' => __( 'Make sure the dimensions are formatted correctly.', 'lucid-slider' ),
			'section' => 'lsjl_image_section',
			'description' => __( 'Separate each image size with a new line. Format like <strong>800x250</strong> (width&times;height).', 'lucid-slider' )
		)
	);

	/* -Theme integration
	--------------------------------------------------------------------------*/
	$lsjl_settings->section( 'lsjl_integration_section', array(
		'heading' => __( 'Theme integration', 'lucid-slider' ),
		'tab' => 'lsjl_general_settings'
	) );

	$lsjl_settings->field(
		'load_assets',
		__( 'Load bundled assets', 'lucid-slider' ),
		array(
			'type' => 'checklist',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_integration_section',
			'description' => __( 'Load if not using in the theme.', 'lucid-slider' ),
			'options' => array(
				'load_css' => __( 'Load CSS', 'lucid-slider' ),
				'load_js'  => __( 'Load JavaScript', 'lucid-slider' )
			),
			'default' => 1
		)
	);

	$lsjl_settings->field(
		'init_slider',
		__( 'Initialize slider', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_integration_section',
			'inline_label' => __( 'Activate the slider automatically', 'lucid-slider' ),
			'description' => __( 'Calls the <strong>flexslider</strong> JavaScript function with the options from the Slider settings page. Disable if calling it in the theme.', 'lucid-slider' ),
			'default' => 1
		)
	);

	/* -Tools
	--------------------------------------------------------------------------*/
	$lsjl_settings->section( 'lsjl_tools_section', array(
		'heading' => __( 'Tools', 'lucid-slider' ),
		'tab' => 'lsjl_general_settings'
	) );

	$lsjl_settings->field(
		'enable_widget',
		__( 'Widget', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_tools_section',
			'inline_label' => __( 'Enable slider widget', 'lucid-slider' ),
			'description' => __( 'Make a widget available for use in a sidebar.', 'lucid-slider' ),
			'default' => 0
		)
	);

	$lsjl_settings->field(
		'enable_tinymce',
		__( 'Editor', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_tools_section',
			'inline_label' => __( 'Enable visual editor button', 'lucid-slider' ),
			'description' => __( 'Make a UI available for visual shortcode insertion', 'lucid-slider' ),
			'default' => 0
		)
	);


	/*========================================================================*\
	      =Slider
	\*========================================================================*/

	/* -General
	--------------------------------------------------------------------------*/
	$lsjl_settings->section( 'lsjl_slider_section', array(
		'heading' => __( 'General slider settings', 'lucid-slider' ),
		'tab' => 'lsjl_slider_settings'
	) );

	$lsjl_settings->field(
		's_animation',
		__( 'Animation', 'lucid-slider' ),
		array(
			'type' => 'radios',
			'sanitize' => 'alphanumeric',
			'section' => 'lsjl_slider_section',
			'options' => array(
				'fade' => __( 'Fade', 'lucid-slider' ),
				'slide' => __( 'Slide', 'lucid-slider' )
			),
			'default' => 'fade'
		)
	);

	$lsjl_settings->field(
		's_direction',
		__( 'Sliding direction', 'lucid-slider' ),
		array(
			'type' => 'radios',
			'sanitize' => 'alphanumeric',
			'section' => 'lsjl_slider_section',
			'description' => __( 'Only affects the "slide" animation', 'lucid-slider' ),
			'options' => array(
				'horizontal' => __( 'Horizontal', 'lucid-slider' ),
				'vertical' => __( 'Vertical', 'lucid-slider' )
			),
			'default' => 'horizontal'
		)
	);

	$lsjl_settings->field(
		's_animationLoop',
		__( 'Animation loop', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_section',
			'inline_label' => __( 'Loop the slides', 'lucid-slider' ),
			'description' => __( 'If disabled, directional navigation will be disabled at either end', 'lucid-slider' ),
			'default' => 1
		)
	);

	$lsjl_settings->field(
		's_smoothHeight',
		__( 'Smooth height', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_section',
			'inline_label' => __( 'Animate slide height differences', 'lucid-slider' ),
			'description' => __( 'Only affects horizontal mode', 'lucid-slider' )
		)
	);

	$lsjl_settings->field(
		's_slideshow',
		__( 'Slideshow', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_section',
			'inline_label' => __( 'Animate slider automatically', 'lucid-slider' ),
			'default' => 1
		)
	);

	$lsjl_settings->field(
		's_slideshowSpeed',
		__( 'Slideshow speed', 'lucid-slider' ),
		array(
			'type' => 'text_monospace',
			'section' => 'lsjl_slider_section',
			'description' => __( 'Time each slide is displayed, in milliseconds', 'lucid-slider' ),
			'sanitize' => 'int',
			'default' => 7000
		)
	);

	$lsjl_settings->field(
		's_animationSpeed',
		__( 'Animation speed', 'lucid-slider' ),
		array(
			'type' => 'text_monospace',
			'section' => 'lsjl_slider_section',
			'description' => __( 'Animation time (for fade/slide), in milliseconds', 'lucid-slider' ),
			'sanitize' => 'int',
			'default' => 600
		)
	);

	$lsjl_settings->field(
		's_randomize',
		__( 'Randomize', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_section',
			'inline_label' => __( 'Randomize slide order', 'lucid-slider' )
		)
	);

	/* -Controls
	--------------------------------------------------------------------------*/
	$lsjl_settings->section( 'lsjl_slider_controls_section', array(
		'heading' => __( 'Controls', 'lucid-slider' ),
		'tab' => 'lsjl_slider_settings'
	) );

	$lsjl_settings->field(
		's_controlNav',
		__( 'Slide navigation', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_controls_section',
			'inline_label' => __( 'Show small navigation buttons for each slide', 'lucid-slider' ),
			'default' => 1
		)
	);

	$lsjl_settings->field(
		's_directionNav',
		__( 'Directional navigation', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_controls_section',
			'inline_label' => __( 'Show previous/next navigation', 'lucid-slider' ),
			'default' => 1
		)
	);

	$lsjl_settings->field(
		's_prevText',
		__( 'Previous text', 'lucid-slider' ),
		array(
			'type' => 'text',
			'section' => 'lsjl_slider_controls_section',
			'description' => __( 'Text for the "previous" directional navigation button, usually only seen by people with screen readers', 'lucid-slider' ),
			'default' => _x( 'Previous', 'Control nav previous text', 'lucid-slider' )
		)
	);

	$lsjl_settings->field(
		's_nextText',
		__( 'Next text', 'lucid-slider' ),
		array(
			'type' => 'text',
			'section' => 'lsjl_slider_controls_section',
			'description' => __( 'Text for the "next" directional navigation button, usually only seen by people with screen readers', 'lucid-slider' ),
			'default' => _x( 'Next', 'Control nav next text', 'lucid-slider' )
		)
	);

	/* -Usability features
	--------------------------------------------------------------------------*/
	$lsjl_settings->section( 'lsjl_slider_usability_section', array(
		'heading' => __( 'Usability features', 'lucid-slider' ),
		'tab' => 'lsjl_slider_settings'
	) );

	$lsjl_settings->field(
		's_pauseOnAction',
		__( 'Pause on action', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_usability_section',
			'inline_label' => __( 'Pause the slideshow when interacting with control elements', 'lucid-slider' ),
			'default' => 1
		)
	);

	$lsjl_settings->field(
		's_pauseOnHover',
		__( 'Pause on hover', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_usability_section',
			'inline_label' => __( 'Pause the slideshow while hovering over slider', 'lucid-slider' ),
			'default' => 1
		)
	);

	$lsjl_settings->field(
		's_touch',
		__( 'Touch', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_usability_section',
			'inline_label' => __( 'Allow touch swipe navigation', 'lucid-slider' ),
			'default' => 1
		)
	);

	$lsjl_settings->field(
		's_video',
		__( 'Video', 'lucid-slider' ),
		array(
			'type' => 'checkbox',
			'sanitize' => 'checkbox',
			'section' => 'lsjl_slider_usability_section',
			'inline_label' => __( 'Using videos in the slider', 'lucid-slider' ),
			'description' => __( 'If set, this will prevent CSS3 3D Transforms to avoid graphical glitches', 'lucid-slider' )
		)
	);

	/**
	 * Runs after default settings are added.
	 *
	 * @param Lucid_Settings $lsjl_settings The settings object, see Lucid
	 *    Toolbox for documentation.
	 */
	do_action( 'lsjl_settings', $lsjl_settings );

endif;

$lsjl_settings->init();