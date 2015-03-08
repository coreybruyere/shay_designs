module.exports = {
  dist: {
    src: [
      '_development/lib//bower_components/unveil/jquery.unveil.js', 
      // '_development/lib//bower_components/slick.js/slick/slick.js',
      '_development/lib//bower_components/owl.carousel/dist/owl.carousel.js',  
      // '_development/lib//bower_components/slick.js/svg4everybody/svg4everybody.js',    
      '_development/lib//javascripts/*.js',
    ],
    dest: '_development/lib//javascripts/production/global.js'
  }
}