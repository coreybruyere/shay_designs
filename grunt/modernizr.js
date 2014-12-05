module.exports = {
  dist: {
    devFile: '_development/lib/bower_components/modernizr/modernizr.js',
    outputFile: '_development/lib/javascripts/production/modernizr-min.js',
    files: {
      src: [
        '_development/lib/javascripts/**/*.js',
        '_development/lib/styles/css/*.css',
      ]
    }
  }
}