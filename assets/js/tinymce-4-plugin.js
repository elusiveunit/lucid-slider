/*!
 * Lucid Slider
 *
 * TinyMCE button functionality. Does an AJAX request with action
 * "lucid_slider_tinymce", which handles the output.
 */

/* global tinymce:true */

tinymce.PluginManager.add( 'lucidSlider', function ( editor ) {
	'use strict';

	editor.addCommand('InsertHorizontalRule', function() {
		editor.execCommand('mceInsertContent', false, '<hr />');
	});

	editor.addButton( 'lucidSlider', {
		title: editor.getLang( 'lucidSlider.title' ),
		onclick: function () {
			tb_show( '', 'admin-ajax.php?action=lucid_slider_tinymce&width=640&height=545' );
		}
	});
});
