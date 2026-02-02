/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

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
	} = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Webring Settings', 'webring' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Webring name', 'webring' ) }
						value={ webringName }
						onChange={ ( value ) =>
							setAttributes( { webringName: value } )
						}
					/>

					<ToggleControl
						label={ __( 'Show copy instructions', 'webring' ) }
						checked={ showCopyInstructions }
						onChange={ ( value ) =>
							setAttributes( { showCopyInstructions: value } )
						}
					/>

					<ToggleControl
						label={ __( 'Show copy button', 'webring' ) }
						checked={ showCopyButton }
						onChange={ ( value ) =>
							setAttributes( { showCopyButton: value } )
						}
					/>

					<ToggleControl
						label={ __( 'Show customization instructions', 'webring' ) }
						checked={ showCustomizationInstructions }
						onChange={ ( value ) =>
							setAttributes( { showCustomizationInstructions: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...useBlockProps() }>
				<ServerSideRender
					block="webring/html-snippet"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}

