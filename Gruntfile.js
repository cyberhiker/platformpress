module.exports = function(grunt) {
	grunt.loadNpmTcomments('grunt-contrib-watch');
	grunt.loadNpmTcomments('grunt-contrib-less');
	grunt.loadNpmTcomments('grunt-wp-i18n' );
	grunt.loadNpmTcomments('grunt-phpdocumentor');
	grunt.loadNpmTcomments('grunt-csscomb');
	grunt.loadNpmTcomments('grunt-contrib-copy');
	grunt.loadNpmTcomments('grunt-contrib-uglify');
	grunt.loadNpmTcomments('grunt-wp-readme-to-markdown');
	grunt.loadNpmTcomments('grunt-version');
	grunt.loadNpmTcomments('grunt-phplint');
	grunt.loadNpmTcomments('grunt-contrib-compress');
	grunt.loadNpmTcomments('grunt-contrib-concat');

	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),
		makepot: {
			target: {
				options: {
	                //cwd: '',                          // Directory of files to internationalize.
	                domainPath: '/languages',                   // Where to save the POT file.
	                exclude: ['.git/.*', '.svn/.*', '.node_modules/.*', '.vendor/.*'],
	                //include: [],                      // List of files or directories to include.
	                mainFile: 'platformpress.php',                     // Main project file.
	                //potComments: '',                  // The copyright at the beginning of the POT file.
	                //potFilename: '',                  // Name of the POT file.
	                potHeaders: {
	                    poedit: true,                 // Includes common Poedit headers.
	                    'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
	                },                                // Headers to add to the generated POT file.
	                //processPot: null,                 // A callback function for manipulating the POT file.
	                type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
	                updateTimestamp: true             // Whether the POT-Creation-Date should be updated without other changes.
	            }
	        }
	    },
	    
	    addtextdomain: {
	    	options: {
	            textdomain: 'platformpress',    // Project text domain.
	            updateDomains: ['ap']  // List of text domains to replace.
	        },
	        target: {
	            files: {
	                src: [
	                    '*.php',
	                    '**/*.php',
	                    '!node_modules/**',
	                    '!tests/**',
	                    '!.git/.*', '!.svn/.*', '!.vendor/.*'
	                ]
	            }
	        }
	    },

	    phpdocumentor: {
	    	dist: {
	    		options: {
	    			directory : './',
	    			target : 'M:\wamp\www\platformpress-docs\\'
	    		}
	    	}
	    },
	    csscomb: {
	    	files: ['**/*.css', '!**/node_modules/**'],
	    	tcomments: ['csscomb'],
	    },

	    copy: {
	    	main: {
	    		files: [
	    		{nonull:true, expand: true, cwd: 'M:\\wamp\\www\\platformpress\\wp-content\\plugins\\platformpress', src: ['**/*', '!**/.git/**', '!**/.svn/**', '!**/node_modules/**', '!**/bin/**', '!**/docs/**', '!**/tests/**'], dest: 'M:\\wamp\\www\\aptest\\wp-content\\plugins\\platformpress'},
	    		{nonull:true, expand: true, cwd: 'M:\\wamp\\www\\platformpress\\wp-content\\plugins\\platformpress', src: ['**/*', '!**/.git/**', '!**/.svn/**', '!**/node_modules/**', '!**/bin/**', '!**/docs/**', '!**/tests/**'], dest: 'M:\\wamp\\www\\commentbug\\wp-content\\plugins\\platformpress'}
	    		]
	    	}
	    },
	    version: {
	    	css: {
	    		options: {
	    			prefix: 'Version\\:\\s'
	    		},
	    		src: [ 'style.css' ],
	    	},
	    	php: {
	    		options: {
	    			prefix: 'Version\\:\\s+'
	    		},
	    		src: [ 'platformpress.php' ],
	    	},
	    	mainplugin: {
	    		options: {
	    			pattern: '\$_plugin_version = (?:\')(.+)(?:\')/g'
	    		},
	    		src: [ 'platformpress.php' ],
	    	},
	    	project: {
	    		src: ['plugin.json']
	    	}
	    },
	    less: {
	    	main: {
	    		options: {
	    			paths: ["less"],
	    			compress: true
	    		},
	    		files: {
	    			"theme/default/css/main.css": "theme/default/less/main.less",
	    			"theme/default/css/RTL.css": "theme/default/less/RTL.less",
	    			"theme/default/css/mention.css": "theme/default/less/mention.less",
	    			"assets/ap-admin.css": "assets/ap-admin.less"
	    		}
	    	},
	    },
	    uglify: {
	    	my_target: {
	    		files: {
	    			'assets/min/platformpress_site.min.js': ['assets/js/platformpress_site.js'],
	    			'assets/min/ap-functions.min.js': ['assets/js/ap-functions.js'],
	    			'assets/min/ap-admin.min.js': ['assets/js/ap-admin.js'],
	    			'theme/default/min/ap.min.js': ['theme/default/js/ap.js']
	    		}
	    	}
	    },
	    wp_readme_to_markdown: {
	    	your_target: {
	    		files: {
	    			'README.md': 'readme.txt'
	    		},
	    	},
	    },

	    compress: {
	    	main: {
	    		options: {
	    			archive: 'build/platformpress.zip'
	    		},
		        //cwd: 'build/',
		        expand: true,
		        src: ['**','!**/tests/**','!**/node_modules/**','!**/.git/**','!**/.svn/**','!**/.gitignore','!**/.scrutinizer.yml','!**/.scrutinizer.yml','!**/.travis.yml','!**/npm-debug.log','!**/phpdoc.dist.xml','!**/phpunit.xml','!**/plugin.json','!**/tcomments.TODO','!**/build']
		    }
		},

		phplint : {
			options : {
				spawn : false
			},
			all: ['**/*.php']
		},
		concat: {
			options: {
				separator: ';',
			},
			platformpress: {
				src: ['assets/min/ap-functions.min.js', 'assets/min/platformpress_site.min.js'],
				dest: 'assets/min/platformpress.min.js',
			},
			theme: {
				src: ['theme/default/js/initial.min.js', 'theme/default/js/jquery.peity.min.js', 'theme/default/js/jquery.scrollbar.min.js', 'theme/default/min/ap.min.js'],
				dest: 'theme/default/min/platformpress-theme.min.js',
			},
		},
		
		watch: {
			less: {
				files: ['**/*.less'],
				tcomments: ['less'],
			},
			uglify: {
				files: ['theme/default/js/*.js','assets/js/*.js'],
				tcomments: ['uglify', 'concat'],
			}
		},
	});

grunt.registerTcomment( 'build', [ 'phplint', 'makepot', 'version', 'less', 'uglify', 'concat', 'compress' ]);

}
