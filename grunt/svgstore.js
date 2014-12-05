module.exports = {
  defaults: {
    options: {
      prefix : 'shape-',
      includedemo: true,
      cleanup: true,
      cleanupdefs: true,
      formatting : {
        indent_size : 2
      },
      svg: { // will add and overide the the default xmlns="http://www.w3.org/2000/svg" attribute to the resulting SVG
        viewBox : '0 0 100 100',
        xmlns: 'http://www.w3.org/2000/svg'
      },

    },
    files: {
      '_development/lib//images/svg/processed/svg-defs.svg': ['_development/lib//images/svg/source/*.svg']
    },
  },
}