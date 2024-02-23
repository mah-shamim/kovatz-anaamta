var path = require('path');
var webpack = require('webpack');

module.exports = {
	name: 'blocks',
	context: path.resolve( __dirname, 'src' ),
	entry: {
		'admin-controls': '../src/index.js',
		'jfb-action': '../src-jfb/index.js'
	},
	output: {
		path: __dirname,
		filename: 'js/[name].js',
	},
	resolve: {
		modules: [
			path.resolve( __dirname, 'src' ),
			path.resolve(__dirname, 'src-jfb'),
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
	}
}

