=== Lucid Slider ===

Contributors: elusiveunit
Tags: slider, slideshow, image slider, image slideshow, simple
Requires at least: 3.5
Tested up to: 3.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple, lightweight slideshow plugin.


== Description ==

Lucid Slider is a simple, lightweight slideshow plugin, with its primary function being an intuitive admin UI. Unlike many other slider plugins, this comes free of bloated options panels with settings galore that confuse novice users. It's primarily meant for theme developers to integrate with, and bring exactly what is needed for the individual site.

The slider itself is powered by the popular [Flexslider](https://github.com/woothemes/FlexSlider) jQuery plugin.


== Installation ==

A standard slider setup is pretty simple:

1. Image sizes must be entered in the settings. In an effort to keep everything as native-like as possible, sizes are added with `add_image_size` and cropped on upload, not on the fly. This most likely means previously uploaded images, and all images if changing sizes, won't have the correct dimensions. Images can be re-cropped with the [AJAX Thumbnail Rebuild](http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/) plugin.
2. As the slider page tells you, one of the added sizes must be chosen for every slider.


== Changelog ==

= 1.2: Jan 24, 2013 =
* New: Can choose 'full' as a slider size, to use full-sized images.
* Fix: Fall back to full-sized images if there isn't an image size matching the chosen one available. This is the case when an uploaded image has the exact same dimensions as an image size (i.e. the image size is 900x250 and the uploaded image is 900x250).
* Fix: Trim whitespace from size values in slider settings.

= 1.1: Jan 13, 2013 =
* New: Use WordPress 3.5 media uploader.
* New: Updated WPAlchemy version to 1.5.2.
* Tweak: Altered the slider management UI slightly.
* Tweak: Added `position: relative;` to flexslider `<li>`'s, so positioning within a slide works out of the box.

= 1.0: Dec 05, 2012 =
* Initial version.


== Developer integration ==

The slider is very barebones by default, only shipping with the standard Flexslider theme and options.

You are encouraged to reduce HTTP requests by bringing JavaScript and CSS into the regular theme files. The Flexslider initialization can be kept in the plugin without a performance hit, which can be useful if a UI for the options is desired. However, this only contains the options related to look and feel; callbacks, selector handling and the like will need to be handled in the theme/plugin. A full list of options can be viewed at the [Flexslider page](http://www.woothemes.com/flexslider/).

### Hooks

There are a number of hooks available, for extending different parts of the plugin.

#### Frontend

##### ljsl\_before\_slide\_image

Runs right after each slide opening `<li>`.
	
	/**
	 * Manipulate slide content.
	 *
	 * @param string $html Current HTML output.
	 * @param int $key Current array index key.
	 * @param array $slide Slide data.
	 * @param int $slider_id ID of the entire slider.
	 * @return string New HTML.
	 */
	function themename_before_slide( $html, $key, $slide, $slider_id ) {
		// Manipulate HTML
		
		return $html;
	}
	add_filter( 'ljsl_before_slide_image', 'themename_before_slide' );

##### ljsl\_after\_slide\_image

Runs right before each slide closing `</li>`.
	
	/**
	 * Manipulate slide content.
	 *
	 * @param string $html Current HTML output.
	 * @param int $key Current array index key.
	 * @param array $slide Slide data.
	 * @param int $slider_id ID of the entire slider.
	 * @return string New HTML.
	 */
	function themename_after_slide( $html, $key, $slide, $slider_id ) {
		// Manipulate HTML
		
		return $html;
	}
	add_filter( 'ljsl_after_slide_image', 'themename_after_slide' );

##### lsjl\_js\_options

Runs before adding the closing curly bracket to the JavaScript options object. Via `$options_added` one can determine if a comma is needed when adding to the object. If `$options_added` is 0 the object will not be echoed no matter the contents, so increase it if needed.
	
	/**
	 * Manipulate JavaScript options.
	 *
	 * @param string $js_options Current options generated from the plugin.
	 * @param int &$options_added Number of options generated.
	 * @return string New options.
	 */
	function themename_slide_options( $js_options, &$options_added ) {
		// Modify options

		return $js_options;
	}
	add_filter( 'lsjl_js_options', 'themename_slide_options', 10, 2 );

#### Backend

##### lsjl\_settings\_tabs

Filters the tabs added to the settings screen.
	
	/**
	 * Add tabs to the Lucis Slider settings.
	 *
	 * @param array $tabs Default tab data.
	 * @return array New tab data.
	 */
	function themename_lsjl_settings_tabs( $tabs ) {
		$tabs['themename_lsjl_test_tab'] = __( 'Test tab', 'themename' );

		return $tabs;
	}
	add_filter( 'lsjl_settings_tabs', 'themename_lsjl_settings_tabs' );

##### lsjl\_settings\_sections

Filters the sections added to the settings screen.
	
	/**
	 * Add sections to the Lucis Slider settings.
	 *
	 * @param array $sections Default section data.
	 * @return array New section data.
	 */
	function themename_lsjl_settings_sections( $sections ) {
		$sections['themename_lsjl_test_section'] = array(
			'heading' => __( 'Test section', 'themename' ),
			'tab' => 'themename_lsjl_test_tab'
		);
		
		return $sections;
	}
	add_filter( 'lsjl_settings_sections', 'themename_lsjl_settings_sections' );

##### lsjl\_settings\_fields

Filters the fields added to the settings screen.
	
	/**
	 * Add fields to the Lucis Slider settings.
	 *
	 * @param array $fields Default field data.
	 * @return array New field data.
	 */
	function themename_lsjl_settings_fields( $fields ) {
		$fields['themename_lsjl_test_field'] = array(
			'label' => __( 'Test field', 'themename' ),
			'section' => 'themename_lsjl_test_section'
		);
		
		return $fields;
	}
	add_filter( 'lsjl_settings_fields', 'themename_lsjl_settings_fields' );

##### lsjl\_include\_alt\_field

Determines if the field for image alt text should be shown on the slider edit screen.
	
	add_filter( 'lsjl_include_alt_field', '__return_false' );

##### lsjl\_meta\_fields\_start

Runs before the default meta data fields for each slide on the slider edit screen.

	/**
	 * Add slide meta field.
	 *
	 * @param object $metabox WPAlchemy metabox object.
	 */
	function themename_lsjl_meta_before( &$metabox ) {
		$metabox->the_field( 'themename_field' ); ?>
		<div class="lsjl-field-group">
			<label for="<?php $metabox->the_name(); ?>"><?php _e( 'Label:', 'themename' ); ?></label>
			<input type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php $metabox->the_value(); ?>">
		</div>
	<?php }
	add_action( 'lsjl_meta_fields_start', 'themename_lsjl_meta_before' );

##### lsjl\_meta\_fields\_end

Runs after the default meta data fields for each slide on the slider edit screen.

Be sure to keep the `lsjl-field-group` on the wrapping div for layout, and add `lsjl-textarea-group` for label alignment when adding a textarea.

	/**
	 * Add slide meta field.
	 *
	 * @param object $metabox WPAlchemy metabox object.
	 */
	function themename_lsjl_meta_after( &$metabox ) {
		$metabox->the_field( 'themename_field' ); ?>
		<div class="lsjl-field-group">
			<label for="<?php $metabox->the_name(); ?>"><?php _e( 'Label:', 'themename' ); ?></label>
			<input type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php $metabox->the_value(); ?>">
		</div>
	<?php }
	add_action( 'lsjl_meta_fields_end', 'themename_lsjl_meta_after' );