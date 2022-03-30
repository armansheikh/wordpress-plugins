var path = require('path')
var webpack = require('webpack')
module.exports = {
  watch: true,
  watchOptions: {
    aggregateTimeout: 500,
    poll: 500,
    // ignored: ['node_modules/**']
  },
  node: {
    fs: "empty",
    module: "empty"
  },
  // entry: './resources/js/im-deals-vue-filters-public.js',
  // output: {
  //   path: path.resolve( __dirname, 'public' ),
  //   publicPath: '/public/',
  //   filename: 'js/[name].js',
  // },
  entry: {
    // frontend and admin will replace the [name] portion of the output config below.
    'im-deals-vue-filters-public': './resources/js/im-deals-vue-filters-public.js',
    // admin: './src/admin/admin-index.js'
  },

  // Create the output files.
  // One for each of our entry points.
  output: {
    // [name] allows for the entry object keys to be used as file names.
    filename: 'js/[name].js',
    // Specify the path to the JS files.
    path: path.resolve( __dirname, 'public' )
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          'vue-style-loader',
          'css-loader'
        ],
      },      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
          }
          // other vue-loader options go here
        }
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/
      },
      {
        test: /\.(png|jpg|gif|svg)$/,
        loader: 'file-loader',
        options: {
          name: '[name].[ext]?[hash]'
        }
      }
    ]
  },
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue.esm.js'
    },
    extensions: ['*', '.js', '.vue', '.json']
  },
  devServer: {
    historyApiFallback: true,
    noInfo: true,
    overlay: true
  },
  performance: {
    hints: false
  },
  devtool: '#eval-source-map'
}

if (process.env.NODE_ENV === 'production') {
  module.exports.devtool = '#source-map'
  // http://vue-loader.vuejs.org/en/workflow/production.html
  module.exports.plugins = (module.exports.plugins || []).concat([
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: '"production"'
      }
    }),
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: true,
      compress: {
        warnings: false
      }
    }),
    new webpack.LoaderOptionsPlugin({
      minimize: true
    })
  ])
}
