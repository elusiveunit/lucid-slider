# Lucid Slider

Lucid Slider is a simple, lightweight slideshow plugin, with its primary function being an intuitive admin UI. Unlike many other slider plugins, this comes free of bloated options panels with settings galore that confuse novice users. It's primarily built for theme developers to integrate with, and bring exactly what is needed for the individual site.

The slider itself is powered by the popular [Flexslider](https://github.com/woothemes/FlexSlider) jQuery plugin.

**Requires [Lucid Toolbox](https://github.com/elusiveunit/lucid-toolbox)**, which is a plugin with a set of classes used to speed up and automate common tasks. This is kept as a separate plugin for easier development and updates. **This plugin will try to install and/or activate Lucid Toolbox** on plugin activation, if it's not available. It simply unzips a bundled version to the directory one level above its install location, if it's not there already, and runs `activate_plugin`.

Lucid Slider is currently available in the following languages:

* English
* Swedish

## Basic usage

A standard slider setup is pretty simple:

1. Image sizes must be entered in the settings. In an effort to keep everything as native-like as possible, sizes are added with `add_image_size` and cropped on upload, not on the fly. This most likely means previously uploaded images, and all images if changing sizes, won't have the correct dimensions. Images can be re-cropped with the [AJAX Thumbnail Rebuild](http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/) plugin, by selecting the desired `lsjl_...` image.
2. As the slider page tells you, one of the added sizes (or full size) must be chosen for every slider.
3. Simply add the desired slides and order them with drag-and-drop.
4. Display the slider with the shortcode `[lucidslider id="123"]` (visual editor button available), the included widget (if activated), or the template tag `lucid_slider( 123 );`.

## Developer integration

The slider is very barebones by default, only shipping with the standard Flexslider theme and options.

You are encouraged to reduce HTTP requests by bringing JavaScript and CSS into the regular theme files. The Flexslider initialization can be kept in the plugin without a performance hit, which can be useful if a UI for the options is desired. However, this only contains the options related to look and feel; callbacks, selector handling and the like will need to be handled in the theme/plugin. A full list of options can be viewed at the [Flexslider page](http://www.woothemes.com/flexslider/).

### Hooks

There are a number of hooks available, for extending different parts of the plugin.

#### Frontend

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

-----

#### Backend

##### lsjl\_templates

Add templates to select. The screenshot is optional, but recommended. The screenshot container has a size of 250x150 pixels. See the template section for more details.

	/**
	 * Add a slider template.
	 *
	 * @param array $templates Templates to add.
	 * @return array
	 */
	function themename_add_slider_template( $templates ) {
		$templates['unique_template_name'] = array(
			'name' => __( 'User-visible name', 'textdomain' ),
			'path' => 'path/to/template-display-file.php',
			'screenshot' => 'URL/to/screenshot.jpg'
		);

		return $templates;
	}
	add_filter( 'lsjl_templates', 'themename_add_slider_template' );

-----

##### lsjl\_show\_template\_metabox

Whether to show the template selection metabox. Hiding it still means any previously saved values are there. If no template is set, the default is loaded. The default template can be overridden with `lsjl_templates`, but there is always a default.

	add_filter( 'lsjl_show_template_metabox', '__return_false' );

-----

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

-----

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

-----

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

-----

##### lsjl\_slides\_meta\_start and lsjl\_slides\_meta\_end

Runs at the start and the end, respectively, of the slides metabox. Can be used to add custom fields, or any other content, to the slider itself.

	/**
	 * Add slider meta field.
	 *
	 * @param object $metabox WPAlchemy metabox object.
	 */
	function themename_lsjl_slider_meta( &$metabox ) {
		$metabox->the_field( 'themename_field' ); ?>
		<div class="lsjl-field-group">
			<label for="<?php $metabox->the_name(); ?>"><?php _e( 'Slider description:', 'themename' ); ?></label>
			<input type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php $metabox->the_value(); ?>">
		</div>
	<?php }
	add_action( 'lsjl_slides_meta_start', 'themename_lsjl_slider_meta' );

-----

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

-----

##### lsjl\_slider\_size\_select

Used to add additional selectable image sizes to the slider size dropdown. The value should be the name of the size (first argument to `add_image_size`).

	/**
	 * Add selectable slider image sizes.
	 *
	 * @param object $metabox WPAlchemy metabox object.
	 */
	function themename_lsjl_extra_sizes( &$metabox ) { ?>
		<option value="image_size_name"<?php $metabox->the_select_state( 'image_size_name' ); ?>>800&times;300</option>
	<?php }
	add_action( 'lsjl_slider_size_select', 'themename_lsjl_extra_sizes' );

## Templates

The plugin currently only comes with the default flexslider template, more can be added with the `lsjl_templates` hook (see hooks section for code).

The default template in the plugin can be used as a starting point for a custom one. It should contain a `<div class="flexslider">`, with the slides inside a `<ul class="slides">` (with `<li>`s, naturally) to function properly.

In the template file (set with 'path' in the hook callback array), there are some key variables available:

* `$slides` contains all the slides and their meta data. This will be looped over to display every slide. The keys `slide-image-thumbnail` and `slide-image-url` (which isn't a full URL anymore) are only saved for admin purposes.
* `$options` has slider options, which at this time of writing is only the slider size.
* `$slides_urls` has image URLs for every slide. Using these instead of grabbing the image with `Lucid_Slider_Utility::get_slide_image_src` saves database requests for every slide. Formatted as `slide_id => URL`.

## Changelog

### 1.5.0: Sep 17, 2013

**Requires 3.6+**

* New, removed: Replace the undocumented `lsjl_fields_meta` action with `lsjl_slides_meta_start` and also add `lsjl_slides_meta_end`. See 'Hooks' documentation section.
* New: Add `$slider` as slider template alias to the slider object.
* Tweak: Update the FlexSlider script to 2.2.0.
* Tweak: Set `jquery-core` as a FlexSlider dependency, to skip jQuery migrate.

### 1.4.2: May 05, 2013

* New: Allow custom sizes to be added to the slider size dropdown with the `lsjl_slider_size_select` action. Example in the 'Hooks' documentation section.
* Fix: Properly load the TinyMCE plugin.
* Fix/tweak: Save image size name instead of dimensions. Passing a size array to `wp_get_attachment_image_src` has occasionally resulted in the wrong URL returned, something I have not observed when passing an image size name. **Re-save sliders to ensure correct results**.
* Tweak: Increase some slider CSS specificity to handle rules like Twenty Twelve's `.entry-content ul`.

### 1.4.1: Apr 14, 2013

**Sin:** This would be 1.5.0, but version numbers have been retroactively changed. Plugin has until this point only been used internally anyway (so why have I kept a changelog?). Moving on!

* New: **Now requires [Lucid Toolbox](https://github.com/elusiveunit/lucid-toolbox)**. Be sure to install it before updating.
* New: Now includes an uninstall file that will remove options and slider posts when uninstalling (removing, not deactivating) the plugin.
* New: Now includes a Grunt build script.
* Tweak: Revert derp moment in 1.2.2 by not explicitly enqueueing jQuery on every page. `wp_enqueue_script` works fine in the body, so jQuery will only be loaded on pages with a slider (unless something else loads it everywhere of course).
* Tweak: Fix some script problems, detected with the new Grunt build process.
* Tweak: Remove -o- prefix from default CSS. Unprefixed since Opera 12.10 and Opera users tend to be good at updating.
* Tweak: Renamed constants to something longer and less likely to conflict with others.

### 1.3.0: Feb 10, 2013

* New: The slider display code is now more of a template system, with separate view files, heavily inspired by [Cyclone Slider 2](http://wordpress.org/extend/plugins/cyclone-slider-2/). This means the hooks `ljsl_before_slide_image` and `ljsl_before_slide_image` are gone. Templates are added with the new `lsjl_templates` filter. See the developer integration section.
* Removed: Two hooks for slide meta fields have been removed: `lsjl_meta_fields_start` action, which ran before the default fields, and `lsjl_include_alt_field` filter. Adding fields before the default ones felt pretty pointless and the alt text field should always be present to encourage accessibility and SEO. Extra meta fields now have their own wrapping `<div>`, for a more robust toggle check.

### 1.2.2: Feb 05, 2013

* Fix: Explicitly enqueue jQuery, since the manual loading introduced in 1.2.1 won't ensure jQuery being loaded.
* Fix: Load metaboxes if $pagenow is null, which is at this time the case in multisite.

### 1.2.1: Feb 01, 2013

* Tweak: Slide image URLs are now saved as post meta, which will save two database request for every slide on a page. Re-save every slider for a performace boost!
* Tweak: The slider JavaScript is now loaded manually in the footer, only if there is a slider on the page (and JavaScript loading by the plugin is enabled in the settings).

### 1.2.0: Jan 27, 2013

* New: A button is now available in the visual editor, which enables an easy UI for shortcode insertion.
* New: A widget is now available for displaying a slider in a sidebar or other widget area.
* New: The visual editor button and the widget can be toggled in the settings. They are disabled by default.
* New: If adding fields to the slider edit screen via hooks, the additional fields are now hidden from view, to keep a compact look for an easy overview. A toggle link is shown if there are more fields than the default two.
* Fix: Keep track of how many sliders have been initialized and only print a single flexslider JavaScript init.
* Fix: Prevent some notices for slides that have meta content but no image.
* Tweak: Errors for when no slider is found are now displayed with `trigger_error` when `WP_DEBUG` is active.
* Tweak: Separate the HTML readme as 'documentation' in its own directory and give it a little more flair.

### 1.1.1: Jan 24, 2013

* New: Can choose 'full' as a slider size, to use full-sized images.
* Fix: Fall back to full-sized images if there isn't an image size matching the chosen one available. This is the case when an uploaded image has the exact same dimensions as an image size (i.e. the image size is 900x250 and the uploaded image is 900x250).
* Fix: Trim whitespace from size values in slider settings.

### 1.1.0: Jan 13, 2013

* New: Use WordPress 3.5 media uploader.
* New: Update WPAlchemy version to 1.5.2, which fixes an issue where repeating metaboxes would not work in WordPress 3.5.
* Tweak: Alter the slider management UI slightly.
* Tweak: Add `position: relative;` to flexslider `<li>`'s, so positioning within a slide works out of the box.

### 1.0.0: Dec 05, 2012

* Initial version.