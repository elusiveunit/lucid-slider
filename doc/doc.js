/*!
 * Lucid Slider
 * 
 * Readme script
 */
(function() {
	'use strict';

	if ( ! document.querySelectorAll ) {return;}

	var headings = document.querySelectorAll( 'h2, h5' ),
	    linkList = document.createElement('ul'),
	    content = '',
	    inSubmenu = false,
	    prevId = '';

	function is_touch() {
		// ontouchstart for most browsers, onmsgesturechange for IE10
		return !!( 'ontouchstart' in window ) || !!( 'onmsgesturechange' in window );
	}

	content += '<li><a href="#intro">Intro</a></li>';

	for ( var i = 0, len = headings.length; i < len; i++ ) {
		if ( headings[i].id ) {

			if ( ! inSubmenu && 'H5' === headings[i].nodeName ) {
				inSubmenu = true;
				content += '\n<ul>';
			} else if ( inSubmenu && 'H5' !== headings[i].nodeName ) {
				inSubmenu = false;
				content += '</li>\n</ul></li>';
			} else {
				content += '</li>';
			}

			content += '\n<li><a href="#' + headings[i].id + '">' + headings[i].innerHTML + '</a>';

			if ( i === len - 1 ) {
				content += '</li>';
				console.log( content );
			}
		}
	}

	if ( is_touch() ) {
		document.documentElement.className = document.documentElement.className.replace(/(\s|^)no-touch(\s|$)/, '$1touch$2');
	}

	linkList.id = 'nav';
	linkList.innerHTML = content;
	document.body.insertBefore( linkList, document.getElementById('lucid-slider') );
})();