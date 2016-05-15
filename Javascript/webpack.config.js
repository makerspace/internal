module.exports = {
	context: __dirname + "/app",
	entry: "./app.js",

	output:
	{
		filename: "app.js",
		path: "../laravel/public/js",
	},

	module: {
		loaders:
		[
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: "babel-loader",
				query: {
					presets: ['es2015', 'react']
				}
			}
		],
	},
}
