# Lucid Slider

[![devDependency Status](https://david-dm.org/elusiveunit/lucid-slider/dev-status.svg)](https://david-dm.org/elusiveunit/lucid-slider#info=devDependencies)

Lucid Slider is a simple, lightweight slideshow plugin, with its primary function being an intuitive admin UI. Unlike many other slider plugins, this comes free of bloated options panels with settings galore that confuse novice users. It's primarily built for theme developers to integrate with and customize, to bring exactly what is needed for each individual site.

The slider itself is powered by the popular [Flexslider](https://github.com/woothemes/FlexSlider) jQuery plugin.

**Requires [Lucid Toolbox](https://github.com/elusiveunit/lucid-toolbox)**, which is a plugin with a set of classes used to speed up and automate common tasks. This is kept as a separate plugin for easier development and updates. **This plugin will try to install and/or activate Lucid Toolbox** on plugin activation, if it's not available. It simply unzips a bundled version to the directory one level above its install location, if it's not there already, and runs `activate_plugin`.

Lucid Slider is currently available in the following languages:

* English
* Swedish


## Features

* Unlimited sliders and slides (obviously, why would there ever be a limit?).
* Easily add and order slides with a simple drag-and-drop interface.
* Uses the familiar WordPress media manager.
* Display sliders with template functions, shortcodes (easily selected through a button in the visual editor), or widgets.
* Extend sliders with additional meta data fields through hooks, and customize the display with your own slider templates.
* **Limitation:** Uses global slider options set on the options page, with no options for individual sliders. The plugin was built with insecure/non-tech savvy clients in mind, which means reducing the clutter to a minimum. Use templates and initialize the slider yourself for more advanced usage.


## Basic usage

A standard slider setup is pretty simple:

1. Add image sizes in the settings if needed. In an effort to keep everything as native-like as possible, sizes are added with `add_image_size` and cropped on upload, not on the fly. This most likely means previously uploaded images, and all images if changing sizes, won't have the correct dimensions. Images can be re-cropped with the [AJAX Thumbnail Rebuild](http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/) plugin, by selecting the desired `lsjl_...` image.
2. As the slider page tells you, one of the added sizes (or full size) must be chosen for every slider.
3. Simply add the desired slides and order them with drag-and-drop.
4. Display the slider with the shortcode `[lucidslider id="123"]` (visual editor button available), the included widget (if activated), or the template tag `lucid_slider( 123 );`.


## Developer integration

The slider is very barebones by default, only shipping with the standard Flexslider theme and options.

You are encouraged to reduce HTTP requests by bringing JavaScript and CSS into the regular theme files. The Flexslider initialization can be kept in the plugin without a performance hit, which can be useful if a UI for the options is desired. However, this only contains the options related to look and feel; callbacks, selector handling and the like will need to be handled in the theme/plugin. A full list of options can be viewed at the [Flexslider page](http://www.woothemes.com/flexslider/).

### Hooks

There are a number of hooks available, for extending different parts of the plugin.

#### Global

##### lsjl\_templates

Add custom templates. The screenshot is optional, but recommended. The screenshot container has a size of 250x150 pixels. See the template section for more details.

	/**
	 * Add a slider template.
	 *
	 * @param array $templates Templates to add.
	 * @return array
	 */
	function myprefix_add_slider_template( $templates ) {
		$templates['unique_template_name'] = array(
			'name' => __( 'User-visible name', 'textdomain' ),
			'path' => 'path/to/template-display-file.php',
			'screenshot' => 'URL/to/screenshot.jpg'
		);

		return $templates;
	}
	add_filter( 'lsjl_templates', 'myprefix_add_slider_template' );

-----

#### Frontend

##### lsjl\_js\_options

Filter the JavaScript Flexslider options. Runs after the options from the plugin settings have been set to the variable.

	/**
	 * Manipulate JavaScript options.
	 *
	 * @param array $js_options Current options generated from the plugin.
	 * @return array New options.
	 */
	function myprefix_slide_options( $js_options ) {

		// Super slideshow!
		$js_options['slideshow'] = true;
		$js_options['slideshowSpeed'] = 1000;

		return $js_options;
	}
	add_filter( 'lsjl_js_options', 'myprefix_slide_options' );

-----

##### lsjl\_slider\_selector

Change the jQuery selector used when initializing the slider (`.flexslider` by default).

	/**
	 * Change the Lucid Slider/Flexslider jQuery selector.
	 *
	 * @param string $selector The original selector.
	 * @return string The new selector.
	 */
	function myprefix_slider_selector( $selector ) {
		return '.my-slider';
	}
	add_filter( 'lsjl_slider_selector', 'myprefix_slider_selector' );

-----

#### Backend

##### lsjl\_show\_template\_metabox

Whether to show the template selection metabox.

Hiding it still means any previously saved values are there. If no template is set, the default is loaded. The default template can be overridden with `lsjl_templates`, but there is always a default.

	add_filter( 'lsjl_show_template_metabox', '__return_false' );

-----

##### lsjl\_show\_all\_slide\_fields

Whether to show all the meta data fields for slides.

By default, any extra fields are hidden and toggled with an anchor. This stops slide items from growing tall and making sorting difficult. It doesn't make much sense when only a single extra field is added though, so toggling can be disabled with this hook.

	add_filter( 'lsjl_show_all_slide_fields', '__return_true' );

-----

##### lsjl\_settings\_tabs

Filters the tabs added to the settings screen. Custom tabs can be added and/or the default ones can be removed.

	/**
	 * Customize Lucis Slider tabs.
	 *
	 * @param array $tabs Tab data.
	 * @return array
	 */
	function myprefix_slider_settings_tabs( $tabs ) {
		$tabs['myprefix_custom_tab'] = __( 'My custom tab', 'myprefix' );

		return $tabs;
	}
	add_filter( 'lsjl_settings_tabs', 'myprefix_slider_settings_tabs' );

-----

##### lsjl\_settings

Action that runs after the default settings have been added. The Lucid_Settings object is passed as an argument, see [Lucid Toolbox](https://github.com/elusiveunit/lucid-toolbox) for documentation.

	/**
	 * Add custom Lucid Slider settings.
	 *
	 * @param Lucid_Settings $settings Settings object.
	 */
	function myprefix_slider_settings( $settings ) {
		$settings->section( 'myprefix_section', array(
			'heading' => __( 'My custom settings section', 'myprefix' ),
			'tab' => 'myprefix_custom_tab'
		) );

		$settings->field(
			'myprefix-field',
			__( 'My custom settings field', 'myprefix' ),
			array( [...] )
		);
	}
	add_action( 'lsjl_settings', 'myprefix_slider_settings' );

-----

##### lsjl\_slides\_meta\_start and lsjl\_slides\_meta\_end

Runs at the start and the end, respectively, of the slides metabox. Can be used to add custom fields, or any other content, to the slider itself.

	/**
	 * Add slider meta field.
	 *
	 * @param WPAlchemy_Metabox $metabox Metabox object.
	 */
	function myprefix_lsjl_slider_meta( $metabox ) {
		$metabox->the_field( 'myprefix-field' ); ?>
		<div class="lsjl-field-group">
			<label for="<?php $metabox->the_name(); ?>"><?php _e( 'Slider description:', 'myprefix' ); ?></label>
			<input type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php $metabox->the_value(); ?>">
		</div>
	<?php }
	add_action( 'lsjl_slides_meta_start', 'myprefix_lsjl_slider_meta' );

-----

##### lsjl\_meta\_fields\_end

Runs after the default meta data fields for each slide on the slider edit screen.

Be sure to keep the `lsjl-field-group` on the wrapping div for layout. Other alignment classes include:

* `lsjl-top-label-group` will, obviously, align the label to the top, which is probably desired with textareas and other taller groups.
* `lsjl-padded-group` will 'indent' the group. Useful for checkboxes that don't have the left-aligned label for indentation.
* Speaking of labels, `lsjl-label` and `lsjl-field-wrap` can be used to treat any elements as a label + field combination. For example, a span as a label + a div wrapping a list of radio buttons, which of course have their own, real labels.

<!-- Don't include the code block in my list, markdown. -->

	/**
	 * Add custom Lucid Slider slide fields.
	 *
	 * @param WPAlchemy_Metabox $metabox Metabox object.
	 */
	function myprefix_lsjl_meta_after( $metabox ) {
		$metabox->the_field( 'myprefix-text' ); ?>
		<div class="lsjl-field-group">
			<label for="<?php $metabox->the_name(); ?>"><?php _e( 'Label:', 'myprefix' ); ?></label>
			<input type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php $metabox->the_value(); ?>">
		</div>

		<?php $metabox->the_field( 'myprefix-radio' ); ?>
		<div class="lsjl-field-group lsjl-top-label-group">
			<p class="lsjl-label"><?php _e( 'Visibility:', 'myprefix' ); ?></p>
			<div class="lsjl-field-wrap">
				<label>
					<input type="radio" name="<?php $metabox->the_name(); ?>" value="visible" <?php $metabox->the_radio_state( 'visible' ); ?>>
					<?php _e( 'Visible', 'myprefix' ); ?>
				</label>
				<br>
				<label>
					<input type="radio" name="<?php $metabox->the_name(); ?>" value="hidden" <?php $metabox->the_radio_state( 'hidden' ); ?>>
					<?php _e( 'Hidden', 'myprefix' ); ?>
				</label>
			</div>
		</div>
	<?php }
	add_action( 'lsjl_meta_fields_end', 'myprefix_lsjl_meta_after' );

-----

##### lsjl\_slider\_size\_select

Used to add additional selectable image sizes to the slider size dropdown. The value should be the name of the size (first argument to `add_image_size`).

	/**
	 * Add selectable slider image sizes.
	 *
	 * @param WPAlchemy_Metabox $metabox Metabox object.
	 */
	function myprefix_lsjl_extra_sizes( $metabox ) { ?>
		<option value="image_size_name"<?php $metabox->the_select_state( 'image_size_name' ); ?>>800&times;300</option>
	<?php }
	add_action( 'lsjl_slider_size_select', 'myprefix_lsjl_extra_sizes' );

## Templates

The plugin currently only comes with the default flexslider template, more can be added with the `lsjl_templates` hook (see hooks section for code).

The default template in the plugin can be used as a starting point for a custom one. It should contain a `<div class="flexslider">`, with the slides inside a `<ul class="slides">` (with `<li>`s, naturally) to function properly.

In the template file (set with 'path' in the hook callback array), there are some key variables available:

* `$slider` is the slider object itself. The below variables are just some aliases for its properties.
* `$slides` contains all the slides and their meta data. This will be looped over to display every slide. The keys `slide-image-thumbnail` and `slide-image-url` (which isn't a full URL anymore) are only saved for admin purposes.
* `$options` has slider options, which at this time of writing is only the slider size.
* `$slides_urls` has image URLs for every slide. Using these instead of grabbing the image with `Lucid_Slider_Utility::get_slide_image_src` saves database requests for every slide. Formatted as `slide_id => URL`.
* `$is_single_slide` is self explanatory. It's primarily intended to be matched with the 'optimize script loading' option, so the first slide can be set to display in the CSS. See the default template.


## Changelog

### 1.7.1: Feb 18, 2015

* Tweak: Always print JavaScript slider options object, even when the automatic init option is turned off. The options can be useful for greater developer control while still keeping them editable through the admin.
* Remove: The workaround for `__FILE__` in symlinked plugins is no longer needed as of WordPress 3.9.

### 1.7.0: Mar 20, 2014

* New: There is now an option to only load the JavaScript if there is more than one slide, since it's not actually needed otherwise. This must be manually activated on existing installs. As can now be seen in the default template, there is a new variable `$is_single_slide` that can be used to check for this case.
* New: The jQuery selector used to initialize the slider can now be changed with the ` lsjl_slider_selector` filter.
* New/fix: Add a new TinyMCE plugin for version 4, which is included in WordPress 3.9.
* Tweak: Sprinkle some media queries on the slider edit screen's input fields, to widen them on larger screens.
* Tweak: Add version flag to `wp_enqueue_script` and `wp_enqueue_style`.

### 1.6.0: Dec 09, 2013

**Contains backward incompatible tweaks (marked with BIT) and requires Lucid Toolbox 1.1.10+**

* New: Include some refreshed admin styling for WordPress 3.8.
* New: Custom settings can be added via the `lsjl_settings` action.
* Tweak (BIT): `lsjl_js_options` now filters the options as an array instead of the concatenated string, which makes it much easier to work with.
* Tweak: The FlexSlider options are now printed as a global LUCID\_SLIDER\_OPTIONS object. This is done in the footer before enqueued scripts are printed, so the options can be modified via JavaScript in addition to the above filter hook.
* Tweak (BIT): `lsjl_slides_meta_start/end`, `lsjl_meta_fields_end` and `lsjl_slider_size_select` actions no longer pass the metabox object by reference, so remove the ampersand from the parameter.
* Tweak/fix: Implemented some unofficial FlexSlider changes. Experimental, may revert.
	* Use unprefixed CSS transitions if available.
	* Try a setTimeout workaround for iOS 7 freeze issue ([#882](https://github.com/woothemes/FlexSlider/pull/882)).
	* Fix vertical scrolling on Windows Phone 8 ([#873](https://github.com/woothemes/FlexSlider/pull/873)).
	* Fix mid transition freeze when scrolling ([#889](https://github.com/woothemes/FlexSlider/pull/889)).
	* Fix freeze after tap ([#768](https://github.com/woothemes/FlexSlider/pull/768)).
	* Ensure currentSlide is treated as an integer in getTarget ([#933](https://github.com/woothemes/FlexSlider/pull/933)).
	* Some minor cleanup.
* Tweak: Generally optimize the admin a bit by limiting what runs where.
* Tweak/fix: Include [this](https://gist.github.com/aubreypwd/7828624) temporary workaround for the issue with `__FILE__` in symlinked plugins, see [trac ticket #16953](http://core.trac.wordpress.org/ticket/16953).
* Fix: Properly apply the `lsjl_settings_tabs` filter.

### 1.5.1: Oct 03, 2013

* New: Add `lsjl_show_all_slide_fields` hook, to disable field collapsing. See backend hook section.
* Tweak: Use default WordPress thumbnail size for slide thumbnails and image stacks, instead of a custom one.
* Tweak: Set CSS and JavaScript to load by default on new installs.

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