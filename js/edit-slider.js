/*!
 * Lucid Slider
 * 
 * Slider edit screen
 */
var lucidSliderEditScreen = (function ( $, win, undefined ) {

	// Settings
	var conf = {
		$slides: $('#lsjl-slides'),
		fieldsCount: 0,

		// jQuery UI sortable settings
		sortableSettings: {
			//items: '.wpa_group-slide-group',
			handle: '.lsjl-move-handle',
			tolerance: 'pointer',
			placeholder: 'lsjl-placeholder',
			forcePlaceholderSize: true,
			stop: function() {
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

	function init() {
		conf.fieldsCount = $('.wpa_group-slide-group.first .lsjl-fields-wrap')[0].children.length;

		bindEvents();
		checkFieldHeight();
	}

	/**
	 * Bind event handlers.
	 */
	function bindEvents() {

		// Load media manager
		conf.$slides.on( 'click', '.lsjl-upload', function( e ) {
			e.preventDefault();
			insertImage( $(this).parents('.wpa_group-slide-group') );
		});

		// Expand hidden meta data fields
		conf.$slides.on( 'click', '.lsjl-expand-group', function( e ) {
			e.preventDefault();
			toggleFields( $(this).prev() );
		});

		$('#wpa_loop-slide-group').sortable( conf.sortableSettings );
	}

	/**
	 * Open media manager and insert an image.
	 *
	 * @param {object} $slide jQuery object for the slide invoking the uploader.
	 */
	function insertImage( $slide ) {
		if ( 'undefined' === typeof $slide ) {return;}

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
			title: $(this).data( 'uploader-title' ),
			button: {
				text: $(this).data( 'uploader-button-text' )
			},
			multiple: false // Can only select a single file
		});

		// When an image is selected, run a callback.
		lucidSliderFrame.on( 'select', function() {
			var image = lucidSliderFrame.state().get('selection').first().toJSON(),
			    thumbnailUrl = image.url;

			thumbnailUrl = thumbnailUrl.split( '.' );
			thumbnailUrl[thumbnailUrl.length - 2] = thumbnailUrl[thumbnailUrl.length - 2] + '-120x80';
			thumbnailUrl = thumbnailUrl.join( '.' );

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
	 * Add class for showing a height toggle link if there are more than two
	 * fields for a slide.
	 */
	function checkFieldHeight() {
		if ( conf.fieldsCount > 2 ) {
			conf.$slides.find('.lsjl-fields-wrap').each(function() {
				$(this).addClass( 'expandable' );
			});
		}
	}

	// Public
	return {

		settings: conf,
		init: init

	};

})( jQuery, window );

// Initialize
jQuery(document).ready(function($) {
	lucidSliderEditScreen.init();
});