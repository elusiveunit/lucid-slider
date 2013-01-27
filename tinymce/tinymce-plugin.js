/*!
 * Lucid Slider
 * 
 * TinyMCE button functionality. Does and AJAX request with action
 * "lucid_slider_tinymce", which handles the output.
 */
(function() {
	tinymce.create('tinymce.plugins.lucidSlider', {
		init : function(ed, url) {

			ed.addButton('lucidSlider', {
				title : ed.getLang( 'lucidSlider.title' ),
				onclick : function() {
					tb_show( '', 'admin-ajax.php?action=lucid_slider_tinymce&width=640&height=545' );
				}
			});

		},

		createControl : function(n, cm) {
			return null;
		}
	});
	tinymce.PluginManager.add('lucidSlider', tinymce.plugins.lucidSlider);
})();