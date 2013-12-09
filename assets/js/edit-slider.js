/*!
 * Lucid Slider
 *
 * Slider edit screen
 */
var lucidSliderEditScreen = (function ( $, win, undefined ) {
	'use strict';

	// Settings
	var conf = {
		$slides: $('#lsjl-slides'),
		$templates: $('#lsjl-slider-template'),
		$templateInputs: null,

		$extraFields: $('.lsjl-extra-fields'),
		$firstWrap: $('.wpa_group-slide-group.first .lsjl-fields-wrap'),
		$firstWrapInner: null,
		fieldsCount: 0,

		// jQuery UI sortable settings
		sortableSettings: {
			//items: '.wpa_group-slide-group',
			handle: '.lsjl-move-handle',
			tolerance: 'pointer',
			placeholder: 'lsjl-placeholder',
			forcePlaceholderSize: true,
			update: function() {
				$('#lsjl-sort-message').css({ 'display': 'block' });
			}
		},

		// jQuery selectors
		selector: {
			thumbnail: '.lsjl-slide-thumbnail',
			thumbnailId: '.lsjl-slide-thumbnail-id', // Image ID
			thumbnailUrl: '.lsjl-slide-thumbnail-field', // URL for the thumbnail, hidden
			url: '.lsjl-slide-url-field' // Image name (previously URL)
		}
	};

	/**
	 * Initialization
	 */
	function init() {
		conf.fieldsCount = conf.$extraFields[0].children.length;
		conf.$templateInputs = conf.$templates.find('input[type="radio"]');

		bindEvents();
		checkFieldExpand();
	}

	/**
	 * Bind event handlers.
	 */
	function bindEvents() {

		// Load media manager
		conf.$slides.on( 'click', '.lsjl-upload', function( e ) {
			e.preventDefault();
			var self = this;
			insertImage( $(self).parents('.wpa_group-slide-group'), self );
		});

		// Expand hidden meta data fields
		conf.$slides.on( 'click', '.lsjl-expand-group', function( e ) {
			e.preventDefault();
			toggleFields( $(this).prev() );
		});

		// Selected class for templates
		conf.$templates.on( 'click', 'input', function() {
			conf.$templateInputs.each(function() {
				$(this).parent().removeClass('selected');
			});

			$(this).parent().addClass('selected');
		});

		// jQuery UI sortable
		$('#wpa_loop-slide-group').sortable( conf.sortableSettings );
	}

	/**
	 * Open media manager and insert an image.
	 *
	 * @param {object} $slide jQuery object for the slide invoking the uploader.
	 * @param {node} button Upload button clicked to show frame.
	 */
	function insertImage( $slide, button ) {
		if ( 'undefined' === typeof $slide ) { return; }

		// Get fields
		var $slideThumbnail = $slide.find( conf.selector.thumbnail ),
		    $idField = $slide.find( conf.selector.thumbnailId ),
		    $thumbnailUrlField = $slideThumbnail.find( conf.selector.thumbnailUrl ),
		    $urlField = $slide.find( conf.selector.url ),
		    $thumbnailImage = $slideThumbnail.find('img'),
		    lucidSliderFrame; // Thumbnail image

		// If the media frame already exists, reopen it.
		if ( lucidSliderFrame ) {
			lucidSliderFrame.open();
			return;
		}

		// Create the media frame.
		lucidSliderFrame = wp.media.frames.lucidSliderFrame = wp.media({
			title: $(button).data( 'uploader-title' ),
			button: {
				text: $(button).data( 'uploader-button-text' )
			},
			multiple: false // Can only select a single file
		});

		// When an image is selected, run a callback.
		lucidSliderFrame.on( 'select', function() {
			var image = lucidSliderFrame.state().get('selection').first().toJSON(),
			    thumbnailUrl;

			if ( image.sizes ) {
				thumbnailUrl = ( 'thumbnail' in image.sizes ) ? image.sizes.thumbnail.url : image.sizes.full.url;
			} else {
				thumbnailUrl = image.icon;
			}

			// Set field values
			$idField.val( image.id );
			$urlField.val( image.filename );
			$thumbnailUrlField.val( thumbnailUrl );
			$thumbnailImage.attr( 'src', thumbnailUrl );
		});

		// Finally, open the modal
		lucidSliderFrame.open();
	}

	/**
	 * Expand to show hidden meta data fields.
	 *
	 * @param {object} $wrap jQuery object for the field wrapper.
	 */
	function toggleFields( $wrap ) {
		$wrap.toggleClass(function() {
			var $button = $wrap.next();

			// Reversed logic since the class isn't added until the return below.
			if ( $wrap.hasClass( 'expanded' ) ) {
				$button.text( $button.data( 'show-text' ) );
			} else {
				$button.text( $button.data( 'hide-text' ) );
			}

			return 'expanded';
		});
	}

	/**
	 * Add class for showing a height toggle.
	 *
	 * Check if there are more than the default two fields, or if the inner wrap
	 * is taller than 70 pixels (about 60 by default).
	 */
	function checkFieldExpand() {
		if ( conf.fieldsCount > 0 ) {
			conf.$slides.find('.lsjl-fields-wrap').addClass( 'expandable' );
		}
	}

	// Public
	return {

		settings: conf,
		init: init

	};

})( jQuery, window );

// Initialize
jQuery(document).ready(function() {
	'use strict';

	lucidSliderEditScreen.init();

});