'use strict'

const path = require('path');
const glob = require("glob");
const autoprefixer = require('autoprefixer');
const miniCssExtractPlugin = require('mini-css-extract-plugin');
const { PurgeCSSPlugin } = require("purgecss-webpack-plugin");

const PATHS = {
  src: path.join(__dirname, "templates"),
};

module.exports = {
  mode: 'development',
  watchOptions: {
    // Set polling so watch works in docker.
    poll: 1000,
  },
  entry: ['./assets/js/lb_accordion.js', './assets/scss/lb_accordion.scss'],
  output: {
    filename: 'lb_accordion.js',
    path: path.resolve(__dirname, 'assets/dist')
  },
  plugins: [
    // Output CSS to its own file.
    new miniCssExtractPlugin({
      filename: 'lb_accordion.css',
    }),
    // Purge CSS of any unused styles.
    new PurgeCSSPlugin({
      paths: glob.sync(`${PATHS.src}/**/*`, { nodir: true }),
    }),
  ],
  module: {
    rules: [
      {
        test: /\.(scss)$/,
        use: [
          {
            // Extracts CSS for each JS file that includes CSS
            loader: miniCssExtractPlugin.loader
          },
          {
            // Interprets `@import` and `url()` like `import/require()` and will resolve them
            loader: 'css-loader'
          },
          {
            // Loader for webpack to process CSS with PostCSS
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins: [
                  autoprefixer
                ]
              }
            }
          },
          {
            // Loads a SASS/SCSS file and compiles it to CSS
            loader: 'sass-loader'
          }
        ]
      }
    ]
  }
}
