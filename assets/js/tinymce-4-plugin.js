/*!
 * Lucid Slider TinyMCE plugin for selcting a slider and inserting its
 * shortcode.
 */

/* global tinymce:true */

tinymce.PluginManager.add( 'lucidSlider', function ( editor ) {
	'use strict';

	var popupTitle = editor.getLang( 'lucidSlider.title' ),
	    popup, getPopupHTML, onButtonClick, onPopupSubmit;

	getPopupHTML = function () {
		var template = document.getElementById( 'lucid-slider-tinymce-content' );

		return ( template ) ? template.innerHTML : '<p>Error: missing template</p>';
	};

	onPopupSubmit = function () {
		var inputs = document.getElementById( 'lsjl-t-select' ).getElementsByTagName( 'input' ),
		    i = inputs.length - 1;

		for ( i; i >= 0; i-- ) {
			if ( inputs[i].checked ) {
				editor.insertContent( '[lucidslider id="' + inputs[i].value + '"]');
				return;
			}
		}
	};

	onButtonClick = function () {
		popup = editor.windowManager.open({
			title: popupTitle,
			width: 480,
			height: 360,
			body: [{
				type: 'container',
				html: getPopupHTML() + '<div id="lsjl-test"></div>'
			}],
			onsubmit: onPopupSubmit
		});
	};

	editor.addButton( 'lucidSlider', {
		title: popupTitle,
		onclick: onButtonClick
	});
});
