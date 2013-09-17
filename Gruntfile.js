module.exports = function(grunt) {
	'use strict';

	grunt.initConfig({

		// Data from package.json
		pkg: grunt.file.readJSON('package.json'),

		// JSHint
		jshint: {
			options: {
				'bitwise'  : true,
				'browser'  : true,
				'curly  '  : true,
				'eqeqeq'   : true,
				'eqnull'   : true,
				'esnext'   : false,
				'forin'    : true,
				'immed'    : true,
				'indent'   : false,
				'jquery'   : true,
				'latedef'  : true,
				'newcap'   : true,
				'noarg'    : true,
				'noempty'  : true,
				'nonew'    : true,
				'node'     : true,
				'smarttabs': true,
				'strict'   : true,
				'trailing' : true,

				// Due to a bunch of globals and plugin params used
				'undef'    : false,
				'unused'   : false,

				'globals': {
					'jQuery': true,
					'alert': true
				}
			},
			dist: {
				src: [
					'assets/js/edit-slider.js',
					'assets/js/tinymce-plugin.js'
				]
			},
			doc: {
				src: ['doc/assets/doc.js']
			},
			grunt: {
				src: ['Gruntfile.js']
			}
		},

		// JavaScript concatenation and minification
		uglify: {
			editSlider: {
				options: {
					report: 'min',
					banner: '/*! <%= pkg.title %> <%= pkg.version %> - Slider edit screen */\n'
				},
				files: [{src: ['assets/js/edit-slider.js'], dest: 'assets/js/edit-slider.min.js'}]
			},
			tinyMCE: {
				options: {
					report: 'min',
					banner: '/*! <%= pkg.title %> <%= pkg.version %> - TinyMCE button */\n'
				},
				files: [{src: ['assets/js/tinymce-plugin.js'], dest: 'assets/js/tinymce-plugin.min.js'}]
			},
			doc: {
				options: {
					report: 'min',
					banner: '/*! <%= pkg.title %> <%= pkg.version %> - Readme script */\n'
				},
				files: [{src: ['doc/assets/doc.js'], dest: 'doc/assets/doc.min.js'}]
			}
		},

		// CSS concatenation and minification
		cssmin: {
			editSlider: {
				options: {
					banner: '/*! <%= pkg.title %> <%= pkg.version %> - Slider edit screen */'
				},
				files: [{src: ['assets/css/edit-slider.css'], dest: 'assets/css/edit-slider.min.css'}]
			},
			tinyMCE: {
				options: {
					banner: '/*! <%= pkg.title %> <%= pkg.version %> - TinyMCE button */'
				},
				files: [{src: ['assets/css/tinymce-plugin.css'], dest: 'assets/css/tinymce-plugin.min.css'}]
			},
			flexslider: {
				options: {
					banner: '/*! <%= pkg.title %> <%= pkg.version %> - jQuery FlexSlider, http://www.woothemes.com/flexslider/ */'
				},
				files: [{src: ['assets/css/flexslider.css'], dest: 'assets/css/flexslider.min.css'}]
			},
			doc: {
				options: {
					banner: '/*! <%= pkg.title %> <%= pkg.version %> - Readme style */'
				},
				files: [{src: ['doc/assets/doc.css'], dest: 'doc/assets/doc.min.css'}]
			}
		},

		// Compile markdown
		markdown: {
			doc: {
				//files: ['README.md'],
				files: [{
					expand: true,
					src: 'README.md',
					dest: 'doc/',
					ext: '.html'
				}],
				options: {
					template: 'doc/assets/template.html',
					gfm: false, // Github flavored markdown
					highlight: function(code, lang) {
						return code; // No code highlighting
					}
				}
			}
		},

		// Rename files
		rename: {
			doc: {
				src: 'doc/README.html',
				dest: 'doc/index.html'
			}
		}

	});

	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-markdown');
	grunt.loadNpmTasks('grunt-rename');

	// Register tasks.
	// Default: just 'grunt'
	grunt.registerTask('default', [
		'jshint:dist',
		'uglify',
		'cssmin'
	]);

	// Documentation: 'grunt doc'
	grunt.registerTask('doc', [
		'jshint:doc',
		'uglify:doc',
		'cssmin:doc',
		'markdown:doc',
		'rename:doc'
	]);

	// Watch: 'grunt w'
	grunt.registerTask('w', [
		'jshint:dist',
		'uglify',
		'cssmin',
		'watch'
	]);

	// Gruntfile: 'grunt g'
	grunt.registerTask('g', [
		'jshint:grunt'
	]);

};