/*!
 * Lucid Slider
 * 
 * Slider edit screen
 */
jQuery(document).ready(function($) {
	var $idField, // Image ID
	    $urlField, // Image URL
	    $thumbnailUrlField, // URL for the thumbnail, hidden
	    $thumbnailImage, // Thumbnail image
	    lucidSliderFrame; // Media upload frame

	/*---------- Media uploader ----------*/
	$('#lsjl-slides').on( 'click', '.lsjl-upload', function( e ) {
		e.preventDefault();

		var $self = $(this),
		    $slide = $self.parent().parent(),
		    $slideThumbnail = $slide.find('.lsjl-slide-thumbnail');

		// Get fields
		$idField = $slide.find('.lsjl-slide-thumbnail-id');
		$urlField = $self.prev();
		$thumbnailUrlField = $slideThumbnail.find('.lsjl-slide-thumbnail-field');
		$thumbnailImage = $slideThumbnail.find('img');

		// If the media frame already exists, reopen it.
		if ( lucidSliderFrame ) {
			lucidSliderFrame.open();
			return;
		}

		// Create the media frame.
		lucidSliderFrame = wp.media.frames.lucidSliderFrame = wp.media({
			title: jQuery(this).data( 'uploader-title' ),
			button: {
				text: jQuery(this).data( 'uploader-button-text' )
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
	});

	/*---------- Sort slides ----------*/
	$('#wpa_loop-slide-group').sortable({
		//items: '.wpa_group-slide-group',
		handle: '.lsjl-move-handle',
		tolerance: 'pointer',
		placeholder: 'lsjl-placeholder',
		forcePlaceholderSize: true,
		stop: function() {
			$('#lsjl-sort-message').css({ 'display': 'block' });
		}
	});










	// Pre 3.5 thickbox uploader

	/*---------- Invoke media upload ----------*/
	/*$('#lsjl-slides').on( 'click', '.lsjl-upload', function( e ) {
		var $self = $(this),
		    $slide = $self.parent().parent(),
		    $slideThumbnail = $slide.find('.lsjl-slide-thumbnail');

		e.preventDefault();

		// Get fields
		$idField = $slide.find('.lsjl-slide-thumbnail-id');
		$urlField = $self.prev();
		$thumbnailUrlField = $slideThumbnail.find('.lsjl-slide-thumbnail-field');
		$thumbnailImage = $slideThumbnail.find('img');

		tb_show( '', 'media-upload.php?lsjl-from-slide-insert=lucid-slider&post_id=0&TB_iframe=1&width=640&height=545' );

		return false;
	});*/

	/*---------- Send the attributes to the form ----------*/
	/*window.send_to_editor = function( html ) {

		    // Extract <img> in case it's wrapped in a link
		var imgHtml = html.match( /(<img)[^\>]+(>)/ )[0],
		    mediaUrl = $( imgHtml ).attr('src'),
		    mediaClass = $( imgHtml ).attr('class'),

		    // Looking for wp-image-#, wrapped in parens to extract the ID
		    imageId = mediaClass.match( /wp-image-(\d+)/ ),

		    // Looking for pattern -300x400.jpg, so this matches
		    // -<digits>x<digits>.<2-4 letters> at the end of a string.
		    // The .extension part is not included in the capture.
		    sizeRegex = /(-\d+x\d+)(?=\.[a-zA-Z]{2,4}$)/,
		    fieldUrl = mediaUrl.replace( sizeRegex, '' ),
		    thumbnailUrl;

		tb_remove();

		// Get regex match, or set to empty
		imageId = ( undefined !== typeof( imageId[1] ) ) ? imageId[1] : '';

		// Only get the file name for the field
		fieldUrl = fieldUrl.split( '/' );
		fieldUrl = fieldUrl[fieldUrl.length - 1]

		// If an image size is chosen, the URL will end in something like
		// -300x400.jpg. In that case, replace it with the thumbnail size,
		// otherwise add the size to the string before the extension.
		if ( sizeRegex.test( mediaUrl ) ) {
			thumbnailUrl = mediaUrl.replace( sizeRegex, '-120x80' );
		} else {
			thumbnailUrl = mediaUrl.split( '.' );
			thumbnailUrl[thumbnailUrl.length - 2] = thumbnailUrl[thumbnailUrl.length - 2] + '-120x80';
			thumbnailUrl = thumbnailUrl.join( '.' );
		}

		// Set field values
		$idField.val( imageId );
		$urlField.val( fieldUrl );
		$thumbnailUrlField.val( thumbnailUrl );
		$thumbnailImage.attr( 'src', thumbnailUrl );
	}*/

});
