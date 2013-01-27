<?php
/**
 * Localization for the TinyMCE plugin.
 *
 * http://wordpress.stackexchange.com/questions/44785/how-to-provide-translations-for-a-wordpress-tinymce-plugin
 * 
 * @package Lucid_Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

$strings = 'tinyMCE.addI18n(
	{' . _WP_Editors::$mce_locale . ': {
			lucidSlider: {
				title: "' . esc_js( __( 'Insert a slider', 'lucid-slider' ) ) . '"
			}
		}
	}
)';