module.exports = {
  dynamic: {
    files: [{
      expand: true,
      cwd: '_development/lib/images/',
      src: ['*.{png,jpg,gif}'],
      dest: '_development/lib/images/min/'
    }]
  },
  options: {
  	cache: false 
  },
}