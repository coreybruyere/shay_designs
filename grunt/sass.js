module.exports = {
  dist: {
    options: {
      // add load paths
      loadPath: [
        // '_development/lib//bower_components/bourbon/dist/',
        // '_development/lib//bower_components/neat/app/assets/stylesheets/'
      ],
      // cssmin will minify later
      style: 'expanded',
      sourcemap: true, 
    },
    files: {
      '_development/lib//styles/css/main.css': '_development/lib//styles/scss/main.scss',
      '_development/lib//styles/css/ie.css': '_development/lib//styles/scss/ie.scss'
    }
  }
}