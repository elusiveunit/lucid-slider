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

		// Minify JavaScript
		uglify: {
			doc: {
				files: {'doc/assets/doc.min.js': ['doc/assets/doc.js']}
			},
			dist: {
				files: [{
					expand: true,
					cwd: 'assets/js',
					src: ['*.js', '!*.min.js'],
					dest: 'assets/js',
					ext: '.min.js',
					extDot: 'last'
				}],
			}
		},

		// Minify CSS
		cssmin: {
			doc: {
				files: {'doc/assets/doc.min.css': ['doc/assets/doc.css']}
			},
			dist: {
				files: [{
					expand: true,
					cwd: 'assets/css',
					src: ['*.css', '!*.min.css'],
					dest: 'assets/css',
					ext: '.min.css',
					extDot: 'last'
				}],
			}
		},

		// Compile markdown
		markdown: {
			doc: {
				options: {
					template: 'doc/assets/template.html',
					gfm: false, // Github flavored markdown
					preCompile: function (src, context) {
						// Remove David badge
						return src.replace(/\[!\[devDependency.+Dependencies\)/, '');
					},
				},
				files: {'doc/index.html': ['README.md']}
			}
		}

	});

	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-markdown');

	// Register tasks.
	// Default: just 'grunt'
	grunt.registerTask('default', [
		'jshint:dist',
		'uglify:dist',
		'cssmin:dist'
	]);

	// Documentation: 'grunt doc'
	grunt.registerTask('doc', [
		'jshint:doc',
		'uglify:doc',
		'cssmin:doc',
		'markdown:doc'
	]);

	// Gruntfile: 'grunt g'
	grunt.registerTask('g', [
		'jshint:grunt'
	]);

};