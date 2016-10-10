module.exports = {
	context: __dirname + "/app",
	entry: "./app.jsx",

	output:
	{
		filename: "app.js",
		path: "../laravel/public/js",
	},

	resolve: {
		extensions: ['', '.js', '.jsx']
	},

	module: {
		loaders:
		[
			{
				test: /\.jsx?$/,
				exclude: /node_modules/,
				loader: "babel-loader",
				query: {
					presets: ['es2015', 'react']
				}
			},
		],
	},
}
