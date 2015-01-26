module.exports = {

    css: {
      files: [
        {
          // Move CSS files from _development to _production
          expand: true,
          flatten: true,
          src: ['_development/lib/styles/css/*-min.css'],
          dest: '_production/wp-content/themes/530Press/lib/styles/css/',
          filter: 'isFile'
        }
      ]
    },
    js: {
      files: [
        {
          // Move JS files from _development to _production
          expand: true,
          flatten: true,
          src: ['_development/lib/javascripts/production/*-min.js'],
          dest: '_production/wp-content/themes/530Press/lib/javascripts/',
          filter: 'isFile'
        }
      ]
    },
    dev_css: {
      files: [
        {
          // Move DEV CSS files from _development to _production
          expand: true,
          flatten: true,
          src: ['_development/lib/styles/css/*.css', '_development/lib/styles/css/*.map'],
          dest: '_production/wp-content/themes/530Press/lib/styles/css/',
          filter: 'isFile'
        }
      ]
    },
    dev_js: {
      files: [
        {
          // Move DEV JS files from _development to _production
          expand: true,
          flatten: true,
          src: ['_development/lib/javascripts/production/*.js'],
          dest: '_production/wp-content/themes/530Press/lib/javascripts/', 
          filter: 'isFile'
        }
      ]
    },
    assets: {
      files: [
        {
          // Move images from _development to _production
          expand: true,
          flatten: true,
          src: ['_development/lib/images/*'],  
          dest: '_production/wp-content/themes/530Press/lib/images/min/',
          filter: 'isFile'
        },
        {
          // Move SVG icons from _development to _production
          expand: true,
          flatten: true,
          src: ['_development/lib/images/svg/*'],
          dest: '_production/wp-content/themes/530Press/lib/images/svg/',
          filter: 'isFile'
        },
        {
          // Move font files from _development to _production
          expand: true,
          flatten: true,
          src: ['_development/lib/fonts/*'],
          dest: '_production/wp-content/themes/530Press/lib/fonts/',
          filter: 'isFile'
        },
      ]
    },

}