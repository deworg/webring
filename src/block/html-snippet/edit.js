/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * WordPress dependencies
 */
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update block attributes.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		webringName,
		showCopyInstructions,
		showCopyButton,
		showCustomizationInstructions,
		enableSyntaxHighlighting,
		syntaxHighlightingTheme,
		wrapLines
	} = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Webring Settings', 'webring-manager' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Webring name', 'webring-manager' ) }
						value={ webringName }
						onChange={ ( value ) =>
							setAttributes( { webringName: value } )
						}
					/>

					<ToggleControl
						label={ __( 'Show copy instructions', 'webring-manager' ) }
						checked={ showCopyInstructions }
						onChange={ ( value ) =>
							setAttributes( { showCopyInstructions: value } )
						}
					/>

					<ToggleControl
						label={ __( 'Show copy button', 'webring-manager' ) }
						checked={ showCopyButton }
						onChange={ ( value ) =>
							setAttributes( { showCopyButton: value } )
						}
					/>

					<ToggleControl
						label={ __( 'Show customization instructions', 'webring-manager' ) }
						checked={ showCustomizationInstructions }
						onChange={ ( value ) =>
							setAttributes( { showCustomizationInstructions: value } )
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'Syntax Highlighting Settings', 'webring-manager' ) }
				           initialOpen={ true }>
					<ToggleControl
						label={ __( 'Enable syntax highlighting', 'webring-manager' ) }
						checked={ enableSyntaxHighlighting }
						onChange={ ( value ) => setAttributes( { enableSyntaxHighlighting: value } ) }
					/>
					{ enableSyntaxHighlighting && (
						<>
							<SelectControl
								label={ __( 'Select syntax highlighting theme', 'webring-manager' ) }
								value={ syntaxHighlightingTheme }
								options={ [
									{ label: 'Light', value: 'prism-theme-default' },
									{ label: 'Dark', value: 'prism-theme-tomorrow' },
									{ label: 'Okaidia', value: 'prism-theme-okaidia' }
								] }
								onChange={ ( value ) => setAttributes( { syntaxHighlightingTheme: value } ) }
							/>
							<ToggleControl
								label={ __( 'Wrap lines', 'webring-manager' ) }
								checked={ wrapLines }
								onChange={ ( value ) => setAttributes( { wrapLines: value } ) }
							/>
						</>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...useBlockProps() }>
				<ServerSideRender
					block="webring-manager/html-snippet"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}

