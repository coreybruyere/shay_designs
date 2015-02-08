module.exports = {
  dist: {
    devFile: '_development/lib/bower_components/modernizr/modernizr.js',
    outputFile: '_development/lib/javascripts/production/modernizr-min.js',
    extra : {
        cssclasses : true
    },
    files: {
      src: [
        '_development/lib/javascripts/**/*.js',
        '_development/lib/styles/css/*.css',
      ]
    },
  },
  options: {
    browsers: ['last 2 version', 'ie 8', 'ie 9', 'Opera 12.1']
  }
}