const path = require('path');
const webpack = require('webpack');

module.exports = {
	name: 'js_bundle',
	context: path.resolve(__dirname, 'src'),
	entry: {
		'blocks.js': './blocks.js',
		'jet-forms.js': './jet-forms.action.js'
	},
	output: {
		path: __dirname,
		filename: '[name]'
	},
	resolve: {
		modules: [
			path.resolve(__dirname, 'src'),
			'node_modules'
		],
		extensions: ['.js'],
		alias: {
			'@': path.resolve(__dirname, 'src'),
			'bases': path.resolve(__dirname, 'src/js/bases/'),
			'filters': path.resolve(__dirname, 'src/js/filters/'),
			'modules': path.resolve(__dirname, 'src/js/modules/'),
			'includes': path.resolve(__dirname, 'src/js/includes/'),
			'blocks': path.resolve(__dirname, 'src/js/blocks/')
		}
	},
	externals: {
		jquery: 'jQuery'
	},
	plugins: [
		new webpack.ProvidePlugin({
			jQuery: 'jquery',
			$: 'jquery'
		})
	],
	optimization: {
		splitChunks: {
			chunks: 'all'
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