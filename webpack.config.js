// webpack.config.js at project root
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
// For latest @wordpress/scripts, this may be an array; see below.

module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		'sidebar-plugin': './src/sidebar-plugin/index.js',
	},
};
