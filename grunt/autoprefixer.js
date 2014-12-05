module.exports = {
  options: {
    browsers: ['last 3 version']
  },
  multiple_files: {
    expand: true,
    flatten: true,
    src: '_development/lib//styles/css/*.css',
    dest: '_development/lib//styles/css/',
  },
}