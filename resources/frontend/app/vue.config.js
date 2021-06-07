const path = require("path");
const vueSrc = "./src";

module.exports = {
  devServer: {
    proxy: 'http://post-project.test'
  },

  // output built static files to Laravel's public dir.
  // note the "build" script in package.json needs to be modified as well.
  outputDir: '../../../public/assets/app',

  publicPath: process.env.NODE_ENV === 'production'
    ? '/assets/app/'
    : '/',

  // modify the location of the generated HTML file.
  indexPath: process.env.NODE_ENV === 'production'
    ? '../../../resources/views/app.blade.php'
    : 'index.html',

  configureWebpack: {
    resolve: {
      alias: {
        "@": path.resolve(__dirname, vueSrc)
      },
      extensions: ['.js', '.vue', '.json']
    }
  },

  transpileDependencies: [
    'vuetify'
  ]
}
