module.exports = {
  build: {
    src: '_development/lib//javascripts/production/global.js',
    dest: '_development/lib//javascripts/production/global-min.js'
  },
  ecom: {
  	expand: true,
  	flatten: true,
  	cwd: '_production/wp-content/themes/530press/lib/javascripts/frontend/',
  	src: ['*.js', '!*.min.js'],
  	dest: '_production/wp-content/themes/530press/lib/javascripts/frontend/',
  	ext: '.min.js',
  },
}