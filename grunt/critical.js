module.exports = {
  test: {
    options: {
      base: './',
      css: [
          '_production/wp-content/themes/530press/lib/styles/css/main-min.css'
      ],
      width: 320,
      height: 70
    },
    src: 'test/fixture/index.html',
    dest: 'test/generated/critical.css'
  }
}