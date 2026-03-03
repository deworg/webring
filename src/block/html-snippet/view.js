/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Import the Prism Syntax Highlighting.
 */
import initPrism from "./prism";

document.addEventListener( "DOMContentLoaded", () => {
	initPrism();
} );

document.addEventListener( 'click', ( event ) => {
	const button = event.target.closest( '.copy-code-button' );
	if (!button) return;

	const targetId = button.dataset.target;
	const codeEl = document.getElementById( targetId );

	if (!codeEl) return;

	const text = codeEl.innerText;

	navigator.clipboard.writeText( text ).then( () => {
		const previousButtonText = button.textContent;
		button.textContent = __( 'Copied!' );
		setTimeout( () => {
			button.textContent = previousButtonText;
		}, 1500 );
	} );
} );


