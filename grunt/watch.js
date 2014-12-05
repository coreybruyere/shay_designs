module.exports = {
  options: {
    livereload: true,
  },
  scripts: {
    files: [
      '_development/lib//javascripts/*.js',
      '_production/wp-content/themes/530press/lib/javascripts/**/*.js'
    ],
    tasks: ['newer:jshint', 'newer:concat', 'newer:uglify', 'newer:copy:dev_js'], 
    options: {
      spawn: false, 
    },
  },
  sass: {
    files: ['_development/lib//styles/scss/*.scss','_development/lib//styles/scss/**/*.scss' ],
    tasks: ['sass', 'newer:autoprefixer', 'newer:cssmin'],
    options: {
      spawn: false,
    },
  },
  css: {
    files: ['_development/lib//styles/css/*.css'],
    tasks: ['newer:copy:dev_css'],
    options: {
      spawn: false,
    },
  },
  images: {
    files: ['_development/lib//images/*.{png,jpg,gif}'],
    tasks: ['newer:imagemin'],
    options: {
      spawn: false,
    },
  },
  svg: {
    files: ['_development/lib//images/svg/source/*.svg'],
    tasks: ['newer:svgstore'],
    options: {
      spawn: false,
    },
  },
  html:{
    files: ['./**/*.html'],
    tasks: [],
    options: {
      spawn: false,
    },
  },
  php:{
    files: ['*.php'],
    tasks: [],
    options: {
      spawn: false,
    },
  },
}