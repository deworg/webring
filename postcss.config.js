const path = require('path');

module.exports = {
	plugins: {
		'postcss-prefix-selector': {
			transform(prefix, selector, prefixedSelector, filePath) {
				if (!filePath) {
					return selector;
				}

				// Only touch Prism theme CSS
				if (!filePath.includes('prismjs/themes/')) {
					return selector;
				}

				// Extract filename: prism-tomorrow.css
				const filename = path.basename(filePath);

				// Extract theme name: tomorrow
				const match = filename.match(/^prism-(.+)\.css$/);

				// Fallback for default prism.css
				const themeName = match ? match[1] : 'default';

				// Build dynamic prefix
				const themePrefix = `.prism-enabled.prism-theme-${themeName}`;

				return `${themePrefix} ${selector}`;
			},
		},
		autoprefixer: {},
	},
};
