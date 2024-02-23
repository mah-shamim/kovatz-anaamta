var path = require('path');
var webpack = require('webpack');

module.exports = {
	name: 'cx-interface-builder',
	context: path.resolve( __dirname, 'src' ),
	entry: {
		'cx-interface-builder.js': 'index.js',
	},
	devtool: 'source-map',
	output: {
		path: path.resolve( __dirname ),
		filename: '[name]'
	},
	resolve: {
		modules: [
			path.resolve( __dirname, 'src' ),
			'node_modules'
		],
		extensions: [ '.js' ],
		alias: {
			'@': path.resolve( __dirname, 'src' ),
		}
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/
			}
		]
	},
	externals: {
		jquery: 'jQuery',
	},
	plugins: [
		new webpack.ProvidePlugin({
			jQuery: 'jquery',
			$: 'jquery'
		}),
	],
	optimization: {
		splitChunks: {
			chunks: 'all'
		}
	},
}

if (process.env.NODE_ENV === 'production') {
	module.exports.plugins = (module.exports.plugins || []).concat([
		new webpack.DefinePlugin({
			'process.env': {
				NODE_ENV: '"production"'
			}
		}),
	]);

	delete module.exports.devtool;
}