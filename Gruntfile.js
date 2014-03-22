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
				'es3'      : true,
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

				'globals': {
					'jQuery': true
				},

				reporter: require('jshint-stylish')
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
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - Slider edit screen */\n'},
				files: {'assets/js/edit-slider.min.js': ['assets/js/edit-slider.js']}
			},
			tinyMCE: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - TinyMCE plugin */\n'},
				files: {'assets/js/tinymce-plugin.min.js': ['assets/js/tinymce-plugin.js']}
			},
			tinyMCE4: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - TinyMCE 4 plugin */\n'},
				files: {'assets/js/tinymce-4-plugin.min.js': ['assets/js/tinymce-4-plugin.js']}
			},
			flexslider: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - jQuery FlexSlider v2.2.0.lucid-1 | Copyright 2012 WooThemes | Contributing Author: Tyler Smith */\n'},
				files: {'assets/js/jquery.flexslider.min.js': ['assets/js/jquery.flexslider.js']}
			},
			doc: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - Readme script */\n'},
				files: {'doc/assets/doc.min.js': ['doc/assets/doc.js']}
			}
		},

		// CSS concatenation and minification
		cssmin: {
			editSlider: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - Slider edit screen */'},
				files: {'assets/css/edit-slider.min.css': ['assets/css/edit-slider.css']}
			},
			editSliderNew: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - Slider edit screen for 3.8+ */'},
				files: {'assets/css/edit-slider-new.min.css': ['assets/css/edit-slider-new.css']}
			},
			tinyMCE: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - TinyMCE plugin */'},
				files: {'assets/css/tinymce-plugin.min.css': ['assets/css/tinymce-plugin.css']}
			},
			tinyMCENew: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - TinyMCE plugin for 3.8+ */'},
				files: {'assets/css/tinymce-plugin-new.min.css': ['assets/css/tinymce-plugin-new.css']}
			},
			flexslider: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - jQuery FlexSlider, http://www.woothemes.com/flexslider/ */'},
				files: {'assets/css/flexslider.min.css': ['assets/css/flexslider.css']}
			},
			doc: {
				options: {banner: '/*! <%= pkg.title %> <%= pkg.version %> - Readme style */'},
				files: {'doc/assets/doc.min.css': ['doc/assets/doc.css']}
			}
		},

		// Compile markdown
		markdown: {
			doc: {
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

	// Gruntfile: 'grunt g'
	grunt.registerTask('g', [
		'jshint:grunt'
	]);

};